<?php

namespace App\Transformers;

use App\Message;
use League\Fractal\TransformerAbstract;

class LogTransformer extends TransformerAbstract
{
    public function transform(Message $message)
    {
        $data = [
            'id'    => $message->_id,
            'type'  => $message->type,
            'user'  => $message->getUser(),
            'text'  => $message->text,
            'ts'    => $message->ts,
        ];

        if (object_get($message, 'subtype')) {
            $data['subtype'] = $message->subtype;
        }

        if (object_get($message, 'attachments')) {
            $data['attachments'] = $message->attachments;
        }

        if (object_get($message, 'file')) {
            $data['file'] = $message->file;
        }

        return $data;
    }

}
