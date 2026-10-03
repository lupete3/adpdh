<?php

namespace App\Exceptions;

use RuntimeException;

class ContactMailException extends RuntimeException
{
    public function __construct(public readonly string $diagnostic, public readonly ?string $transportDetail = null)
    {
        parent::__construct($diagnostic);
    }

    public function explanation(): string
    {
        $explanation = match ($this->diagnostic) {
            'PHP_MAIL_UNAVAILABLE' => 'La fonction PHP mail() est absente ou désactivée sur cet hébergement.',
            'PHP_MBSTRING_UNAVAILABLE' => 'La fonction mb_encode_mimeheader() est indisponible. Activez l’extension PHP mbstring sur cet hébergement.',
            'PHP_MAIL_REFUSED' => 'La fonction mail() a renvoyé false : le service d’envoi local n’a pas accepté le message.',
            'PHP_MAIL_WARNING' => 'PHP a signalé une erreur pendant l’appel à mail() et le message n’a pas été accepté.',
            'PHP_MAIL_EXCEPTION' => 'L’appel à mail() a levé une exception avant de confirmer l’acceptation du message.',
            default => 'Une erreur est survenue pendant la préparation du message.',
        };
        // Used only by the administrator test; the public form keeps a generic error.
        return $explanation.($this->transportDetail ? ' Détail PHP : '.$this->transportDetail : '');
    }
}
