<?php

namespace App\Mail;

use stdClass;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackMailer extends Mailable
{

    use Queueable, SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(stdClass $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
//        if ($this->data->image) {
//            $this->attachData($this->data->image, 'image.'.$this->data->ext);
//        }
        if ($this->data->image) {
            $this->attachFromStorageDisk('local', $this->data->image);
        }
        return $this->from('Larashopion@ion.com', 'name Имя 12345')
            ->subject('New Form Форма')
            ->view('email.feedback', ['data' => $this->data])
            ->withSwiftMessage(function ($message) {
                $message->getHeaders()->addTextHeader('Custom-Header', 'HeaderValue');
            });
//        $this->subject('Форма обратной связи')
//            ->view('email.feedback', compact('this->data'));
    }
}
