<?php

class rex_effect_insert_text extends rex_effect_abstract
{
    /**
     *
     */
    public function execute()
    {
        $this->media->asImage();

        // -------------------------------------- CONFIG
        $fontFile = rex_path::media($this->params['font_file']);
        if (!file_exists($fontFile) || !is_file($fontFile)) {
            return;
        }

        $gdImage = $this->media->getImage();

        // Transparenz erhalten (für GIF, PNG & WebP)
        $this->keepTransparent($output);

        // Text
        $text = 'Text einfügen';
        if (isset($this->params['text'])) {
            $text = (string) $this->params['text'];
        }

        // Schriftgröße
        $fontSize = 24;
        if (isset($this->params['font_size'])) {
            $fontSize = (int) $this->params['font_size'];
        }

        // Farbe
        $color = [0, 0, 0];
        if (isset($this->params['color_r'])) {
            $color[0] = (int) $this->params['color_r'];
        }

        if (isset($this->params['color_g'])) {
            $color[1] = (int) $this->params['color_g'];
        }

        if (isset($this->params['color_b'])) {
            $color[2] = (int) $this->params['color_b'];
        }

        // Abstand vom Rand
        $padding = [0, 0];
        if (isset($this->params['padding_x'])) {
            $padding[0] = (int) $this->params['padding_x'];
        }

        if (isset($this->params['padding_y'])) {
            $padding[1] = (int) $this->params['padding_y'];
        }

        $position = ['right', 'bottom'];
        // Horizontale Ausrichtung: left/center/right
        if (isset($this->params['hpos'])) {
            $position[0] = (string) $this->params['hpos'];
        }

        // Vertikale Ausrichtung:   top/center/bottom
        if (isset($this->params['vpos'])) {
            $position[1] = (string) $this->params['vpos'];
        }

        // Antialiasing
        $antialiasing = 0;
        if (isset($this->params['antialiasing'])) {
            $antialiasing = (int) $this->params['antialiasing'];
        }

        // -------------------------------------- /CONFIG

        $box = imagettfbbox($fontSize, 0, $fontFile, $text);
        $boxWidth = abs($box[6] - $box[2]);

        // Determine cap height
        $box = imagettfbbox($fontSize, 0, $fontFile, 'X É');
        $capHeight = abs($box[7] - $box[1]);

        // Determine descender height
        $box = imagettfbbox($fontSize, 0, $fontFile, 'X Égjpqy');
        $boxHeight = abs($box[7] - $box[1]);

        $fixHeight = $boxHeight - $capHeight;

        $imageWidth = $this->media->getWidth();
        $imageHeight = $this->media->getHeight();

        switch ($position[0]) {
            case 'left':
                $dstX = 0;
                break;
            case 'center':
                $dstX = (int) (($imageWidth - $boxWidth) / 2);
                break;
            case 'right':
            default:
                $dstX = $imageWidth - $boxWidth;
        }

        switch ($position[1]) {
            case 'top':
                $dstY = 0;
                break;
            case 'middle':
                $dstY = (int) (($imageHeight - $boxHeight) / 2);
                break;
            case 'bottom':
            default:
                $dstY = $imageHeight - $boxHeight;
        }

        if ($antialiasing > 0) {
            // Create temp image
            $gdTemp = imagecreatetruecolor($boxWidth * $antialiasing, $boxHeight * $antialiasing);

            // Fill transparent
            imagefill($gdTemp, 0, 0, imagecolorallocatealpha($gdTemp, 255, 255, 255, 127));

            // Write text
            imagettftext(
                $gdTemp,
                $fontSize * $antialiasing,
                0,
                0,
                ($boxHeight - $fixHeight) * $antialiasing,
                imagecolorallocate($gdTemp, $color[0], $color[1], $color[2]),
                $fontFile,
                $text
            );

            // Copy scaled image
            imagecopyresampled(
                $gdImage,
                $gdTemp,
                $dstX + $padding[0],
                $dstY + $padding[1],
                0,
                0,
                $boxWidth,
                $boxHeight,
                $boxWidth * $antialiasing,
                $boxHeight * $antialiasing
            );

            // Free memory
            imagedestroy($gdTemp);

        } else {
            // Write text
            imagettftext(
                $gdImage,
                $fontSize,
                0,
                $dstX + $padding[0],
                $dstY + $padding[1] + $boxHeight - $fixHeight,
                imagecolorallocate($gdImage, $color[0], $color[1], $color[2]),
                $fontFile,
                $text
            );
        }

        $this->media->setImage($gdImage);
    }

    /**
     * @return array|void
     */
    public function getParams()
    {
        return [
            [
                'label' => 'Text',
                'name' => 'text',
                'type' => 'string',
                'default' => '',
            ],
            [
                'label' => 'Schriftgröße',
                'name' => 'font_size',
                'type' => 'int',
                'default' => 12,
            ],
            [
                'label' => 'Schriftdatei',
                'name' => 'font_file',
                'type' => 'media',
                'default' => '',
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_colorize_r'),
                'name' => 'color_r',
                'type' => 'int',
                'default' => 0,
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_colorize_g'),
                'name' => 'color_g',
                'type' => 'int',
                'default' => 0,
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_colorize_b'),
                'name' => 'color_b',
                'type' => 'int',
                'default' => 0,
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_brand_hpos'),
                'name' => 'hpos',
                'type' => 'select',
                'options' => ['left', 'center', 'right'],
                'default' => 'left',
                'attributes' => ['class' => 'selectpicker form-control'],
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_brand_vpos'),
                'name' => 'vpos',
                'type' => 'select',
                'options' => ['top', 'middle', 'bottom'],
                'default' => 'top',
                'attributes' => ['class' => 'selectpicker form-control'],
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_brand_padding_x'),
                'name' => 'padding_x',
                'type' => 'int',
                'default' => 0,
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_brand_padding_y'),
                'name' => 'padding_y',
                'type' => 'int',
                'default' => 0,
            ],
            [
                'label' => 'Antialiasing',
                'name' => 'antialiasing',
                'type' => 'select',
                'options' => range(0, 10),
                'default' => 0,
                'attributes' => ['class' => 'selectpicker form-control'],
            ],
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Bild: Insert Text';
    }
}
