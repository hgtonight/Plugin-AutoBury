<?php if (!defined('APPLICATION')) exit();
/*	Copyright 2016 Zachary Doll
 *	This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2
 *  of the License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$PluginInfo['AutoBury'] = array(
	'Name' => 'Auto Bury',
	'Description' => 'Automatically buries comments and discussions, preventing them from being shown.',
	'Version' => '0.1',
	'RequiredApplications' => array('Vanilla' => '2.2', 'Yaga' => '1.1'),
	'MobileFriendly' => true,
	'HasLocale' => TRUE,
	'RegisterPermissions' => FALSE,
    'SettingsUrl' => '/settings/autobury',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => 'Zachary Doll',
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv2'
);

class AutoBury extends Gdn_Plugin {
	
    public function settingsController_autoBury_create($sender) {
        $sender->SetData('Title', $this->getPluginKey('Name'));
        $sender->Permission('Garden.Settings.Manage');
        $sender->SetData('PluginDescription', $this->GetPluginKey('Description'));
        $validation = new Gdn_Validation();
        $configurationModel = new Gdn_ConfigurationModel($validation);
        $configurationModel->SetField(array(
            'AutoBury.Threshold' => -5,
        ));
        $sender->Form->SetModel($configurationModel);
        
        if ($sender->Form->AuthenticatedPostBack() === FALSE) {
            $sender->Form->SetData($configurationModel->Data);
        } else {
            $configurationModel->Validation->ApplyRule('AutoBury.Threshold', 'Required');
            if ($sender->Form->Save()) {
                $sender->InformMessage('<span class="InformSprite Sliders"></span>' . T('Your changes have been saved.'), 'HasSprite');
            }
        }
        $sender->Render($this->GetView('settings.php'));
    }
    
    public function discussionsController_beforeDiscussionName_handler($sender) {
      $this->bury('Discussion', $sender);
    }
    
    public function discussionController_beforeCommentDisplay_handler($sender) {
      $this->bury('Comment', $sender);
    }
    
    private function bury($objectName, $sender) {
      $object = $sender->EventArguments[$objectName];
      if(!is_null($object->Score) && $object->Score < c('AutoBury.Threshold', -5)) {
          $sender->EventArguments['CssClass'] .= ' Buried';
      }
    }
    
    public function discussionsController_render_before($sender) {
        $this->addResources($sender);
    }
    
    public function discussionController_render_before($sender) {
        $this->addResources($sender);
    }
	
	private function addResources($sender) {
        $sender->addDefinition('AutoBury.Translation', t('This item is buried, click to show'));
		$sender->AddJsFile($this->GetResource('js/autobury.js', FALSE, FALSE));
		$sender->AddCssFile($this->GetResource('design/autobury.css', FALSE, FALSE));
    }
}
