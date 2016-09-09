<?php
/**
 * Nextcloud - passman
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Sander Brand <brantje@gmail.com>
 * @copyright Sander Brand 2016
 */

namespace OCA\Passman\AppInfo;
use OC\Files\View;

use OCA\Passman\Controller\CredentialController;
use OCA\Passman\Controller\FileController;
use OCA\Passman\Controller\PageController;
use OCA\Passman\Controller\RevisionController;
use OCA\Passman\Controller\VaultController;

use OCP\AppFramework\App;
use OCP\IL10N;
use OCP\Util;
class Application extends App {
	public function __construct () {
		parent::__construct('passman');
		$container = $this->getContainer();
		// Allow automatic DI for the View, until we migrated to Nodes API
		$container->registerService(View::class, function() {
			return new View('');
		}, false);
		$container->registerService('isCLI', function() {
			return \OC::$CLI;
		});
		// Aliases for the controllers so we can use the automatic DI
		$container->registerAlias('CredentialController', CredentialController::class);
		$container->registerAlias('FileController', FileController::class);
		$container->registerAlias('PageController', PageController::class);
		$container->registerAlias('RevisionController', RevisionController::class);
		$container->registerAlias('VaultController', VaultController::class);
	}
	/**
	 * Register the navigation entry
	 */
	public function registerNavigationEntry() {
		$c = $this->getContainer();
		/** @var \OCP\IServerContainer $server */
		$server = $c->getServer();
		$navigationEntry = function () use ($c, $server) {
			return [
				'id' => $c->getAppName(),
				'order' => 10,
				'name' => $c->query(IL10N::class)->t('Password'),
				'href' => $server->getURLGenerator()->linkToRoute('passman.PageController.index'),
				'icon' => $server->getURLGenerator()->imagePath($c->getAppName(), 'app.svg'),
			];
		};
		$server->getNavigationManager()->add($navigationEntry);
	}

	/**
	 * Register personal settings for notifications and emails
	 */
	public function registerPersonalPage() {
		\OCP\App::registerPersonal($this->getContainer()->getAppName(), 'personal');
	}
}