<?php

declare(strict_types=1);

namespace Search\Input\Constraint;

use Linio\Component\Input\Constraint\Constraint;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileConstraint extends Constraint
{
    /**
     * @var array
     */
    private $options;

    public function __construct(array $options, $errorMessage = null)
    {
        $this->options = array_merge(
            [
                'min-size' => 0, // in bytes
                'max-size' => PHP_INT_MAX, // in bytes
                'allowed-mime-types' => [],
                'allowed-extensions' => [],
            ],
            $options
        );

        $defaultErrorMessage = sprintf(
            'Problem to upload the file'
        );
        $this->setErrorMessage($errorMessage ?? $defaultErrorMessage);
    }

    public function validate($entry): bool
    {
        if (!$entry instanceof UploadedFile) {
            return false;
        }

        if (!$entry->isValid()) {
            return false;
        }

        $options = $this->options;

        if ($entry->getSize() < $this->options['min-size']) {
            $this->setErrorMessage(sprintf(
                'The minimum size for the file is "%s" bytes',
                $this->options['min-size']
            ));

            return false;
        }

        if ($entry->getSize() > $options['max-size']) {
            $this->setErrorMessage(sprintf(
                'The maximum size for the file is "%s" bytes',
                $options['max-size']
            ));

            return false;
        }

        if (count($options['allowed-mime-types'])
            && !in_array($entry->getClientMimeType(), $options['allowed-mime-types'])
        ) {
            $this->setErrorMessage(sprintf(
                'The allowed mime type(s) for the file is/are "%s"',
                implode(', ', $options['allowed-mime-types'])
            ));

            return false;
        }

        if (count($options['allowed-extensions'])
            && !in_array($entry->getClientOriginalExtension(), $options['allowed-extensions'])
        ) {
            $this->setErrorMessage(sprintf(
                'The allowed extension(s) for the file is/are "%s"',
                implode(', ', $options['allowed-extensions'])
            ));

            return false;
        }

        return true;
    }
}
