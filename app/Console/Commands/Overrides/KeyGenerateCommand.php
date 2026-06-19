<?php

namespace Pterodactyl\Console\Commands\Overrides;

use Illuminate\Foundation\Console\KeyGenerateCommand as BaseKeyGenerateCommand;

class KeyGenerateCommand extends BaseKeyGenerateCommand
{
    /**
     * Override the default Laravel key generation command to throw a warning to the user
     * if it appears that they have already generated an application encryption key.
     */
    public function handle()
    {
        if (!empty(config('app.key')) && $this->input->isInteractive()) {
            $this->output->warning(trans('command/messages.app.key_warning'));
            if (!$this->confirm(trans('command/messages.app.key_accept'))) {
                return;
            }

            if (!$this->confirm(trans('command/messages.app.key_confirm'))) {
                return;
            }
        }

        parent::handle();
    }
}
