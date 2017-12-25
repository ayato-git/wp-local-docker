<?php
use Composer\Script\Event;

class Localizer
{
    public static function postInstallCommand(Event $event)
    {
        if (self::hasRequired($event, 'johnpbloch/wordpress') && self::hasRequired($event, 'wp-cli/wp-cli')) {
            self::localizeWordPress($event);
        }
    }

    public static function postUpdateCommand(Event $event)
    {
        if (self::hasRequired($event, 'johnpbloch/wordpress') && self::hasRequired($event, 'wp-cli/wp-cli')) {
            self::localizeWordPress($event);
        }
    }

    public static function localizeWordPress($event)
    {
        exec('./vendor/bin/wp core is-installed', $stdout, $status);

        if (0 !== $status) {
            return;
        }

        exec('./vendor/bin/wp language core list --field=language --status=active', $stdout, $status);

        $locale = (0 === $status) ? $stdout[0] : '';

        system('./vendor/bin/wp checksum core --locale=' . $locale, $status);
        if ($status !== 0) {
            system('./vendor/bin/wp core download --force --version=$(./vendor/bin/wp core version) --locale=' . $locale, $status);
            Installer::initWordPress($event);
        }

        system('./vendor/bin/wp language core update', $status);
    }

    private static function hasRequired($event, $packageName)
    {
        $packages = $event->getComposer()->getPackage();
        $requires = $packages->getRequires();
        $devs = $packages->getDevRequires();

        return  isset($requires[$packageName]) OR isset($devs[$packageName]);
    }
}
