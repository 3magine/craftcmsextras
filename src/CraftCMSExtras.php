<?php
/**
 * CraftCMSExtras plugin for Craft CMS 3.x
 *
 * Extra twig filters
 *
 * @link      https://www.3magine.com
 * @copyright Copyright (c) 2018 Karl Schellenberg
 */

namespace threemagine\craftcmsextras;


use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;

use yii\base\Event;

/**
 * Class CraftCMSExtras
 *
 * @author    Karl Schellenberg
 * @package   CraftCMSExtras
 * @since     1.0.0
 *
 */
class CraftCMSExtras extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var CraftCMSExtras
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'craft-cmsextras',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

        if (Craft::$app->request->getIsSiteRequest()) {
            // Add in our Twig extension
            $extension = new ExtrasExtension();
            Craft::$app->view->registerTwigExtension($extension);
        }
    }

    // Protected Methods
    // =========================================================================

}

//https://twig.symfony.com/doc/2.x/advanced.html#creating-an-extension
class ExtrasExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_Filter('spanit', array($this,'spanit'), array('is_safe' => array('html'))),
            new \Twig_Filter('numeric', array($this,'numeric')),
        );
    }
    public function spanit($html){
        $d = explode("\n",$html);
        $d = array_map(function($v){
            if(substr($v,0,1) === '*')
                return '<span>'.substr($v,1).'</span>';
            return $v;
        },$d);
        $html = implode("\n",$d);

        return $html;
    }
    public function numeric($html){
        return preg_replace("/[^0-9]/", '', $html);
    }

}

