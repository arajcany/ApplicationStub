<?php

namespace App\Controller\Component;

use Cake\Controller\Component\FlashComponent as CakeFlashComponent;

/**
 * Extension to the Cake Flash component
 * Mainly used to avoid IDE Errors by way of DocBlocks
 *
 * @method void danger(string $message, array $options = []) Set a message using "danger" element
 * @method void dangerHtml(string $message, array $options = []) Set a message using "danger" element
 * @method void default(string $message, array $options = []) Set a message using "default" element
 * @method void defaultHtml(string $message, array $options = []) Set a message using "default" element
 * @method void error(string $message, array $options = []) Set a message using "error" element
 * @method void errorHtml(string $message, array $options = []) Set a message using "error" element
 * @method void info(string $message, array $options = []) Set a message using "info" element
 * @method void infoHtml(string $message, array $options = []) Set a message using "info" element
 * @method void success(string $message, array $options = []) Set a message using "success" element
 * @method void successHtml(string $message, array $options = []) Set a message using "success" element
 * @method void warning(string $message, array $options = []) Set a message using "warning" element
 * @method void warningHtml(string $message, array $options = []) Set a message using "warning" element
 */
class FlashComponent extends CakeFlashComponent
{

    /**
     * Set multiple Flash Messages
     *
     * $messages is a numerically indexed multi-dimensional array with the subkeys that follow the FlashComponent
     *
     * - `message` The message displayed in the GUI
     * - `key` The key to set under the session's Flash key
     * - `element` The element used to render the flash message. Default to 'default'.
     * - `params` An array of variables to make available when using an element
     * - `clear` A bool stating if the current stack should be cleared to start a new one
     * - `escape` Set to false to allow templates to print out HTML content
     *
     * @param array $messages
     */
    public function setMultiple(array $messages = [])
    {
        foreach ($messages as $message) {

            $options = $message + $this->getConfig();
            unset($options['message']);

            $this->set($message['message'], $options);
        }
    }


    /**
     * Try to call the right flash type based on the error string
     *
     * @param string $message
     * @param array $options
     */
    public function smartFlash(string $message, array $options = [])
    {
        if (strpos(strtolower($message), 'error') !== false) {
            $this->error($message, $options);
        } elseif (strpos(strtolower($message), 'warning') !== false) {
            $this->warning(__($message, $options));
        } elseif (strpos(strtolower($message), 'danger') !== false) {
            $this->danger($message, $options);
        } elseif (strpos(strtolower($message), 'success') !== false) {
            $this->success($message, $options);
        } else {
            $this->info(__($message, $options));
        }
    }

}
