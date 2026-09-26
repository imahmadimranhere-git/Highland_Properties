<?php

namespace App\Support;

/**
 * The opening line of a WhatsApp chat started from a project or society page.
 * The wording is a setting so it can be changed without touching a view;
 * {project} and {url} are filled in here.
 */
final class WhatsappMessage
{
    public static function for(string $name, string $url): string
    {
        $template = setting('whatsapp_message') ?: 'Hi, I am interested in {project} — {url}';

        return str_replace(['{project}', '{url}'], [$name, $url], $template);
    }
}
