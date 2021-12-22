<?php

class rex_effect_insert_text extends rex_effect_abstract
{
    /**
     * Generate image with text
     */
    public function execute()
    {

        // -------------------------------------- CONFIG

        $fontFile = rex_path::media($this->params['font_file']);
        if (!file_exists($fontFile) || !is_file($fontFile)) {
            return;
        }

        // Text from Input or Meta
        $text = 'Hello World!';

        if (($this->params['text_source'] === 'input') && isset($this->params['text'])) {
            // Take text from input field
            $text = (string) $this->params['text'];
        } else if (isset($this->params['text_source'])) {
            // Take text from meta field in mediapool
            $text = trim(static::getMeta($this->params['text_source']));
        }

        // Config in first line of text
        //0        1        2     3    4    5          6         7         8           9       10        11
        //fonzsize|font.ttf|color|hpos|vpos|offsetleft|offsettop|antialias|shadowcolor|bgcolor|bgpadding|angle
        $txtparams = [];
        $wtext = explode("\n", $text);
        if (count($wtext) > 1) {
            if (strpos($wtext[0], '|') !== false) {
                $txtparams = explode('|', trim($wtext[0]));
                $txtparams = array_map('trim', $txtparams);
                unset($wtext[0]);
                $text = trim(implode("\n", $wtext));
            }
        }
        if (isset($txtparams[1]) && $txtparams[1] <> '') {
            $fontFile = rex_path::media($txtparams[1]);
            if (!file_exists($fontFile) || !is_file($fontFile)) {
                return;
            }
        }
        if (!$text) {
            return;
        }

        // Font size
        $fontSize = 30;
        if (isset($this->params['font_size']) && $this->params['font_size'] <> '') {
            $fontSize = (int) $this->params['font_size'];
        }
        if (isset($txtparams[0]) && $txtparams[0] <> '') {
            $fontSize = (int) $txtparams[0];
        }

        // Color
        $color = [0, 0, 0];
        $alpha = 0;
        if (isset($this->params['color']) && $this->params['color'] <> '') {
            $rgba = $this->hexToRgb($this->params['color']);
            $color[0] = (int) $rgba['r'];
            $color[1] = (int) $rgba['g'];
            $color[2] = (int) $rgba['b'];
            $alpha = (int) $rgba['a'];
        }
        if (isset($txtparams[2]) && $txtparams[2] <> '') {
            $rgba = $this->hexToRgb($txtparams[2]);
            $color[0] = (int) $rgba['r'];
            $color[1] = (int) $rgba['g'];
            $color[2] = (int) $rgba['b'];
            $alpha = (int) $rgba['a'];
        }

        // Default Position
        $position = ['top', 'center'];

        // Horizontal align: left/center/right
        if (isset($this->params['hpos']) && $this->params['hpos'] <> '') {
            $position[0] = (string) $this->params['hpos'];
        }
        if (isset($txtparams[3]) && $txtparams[3] <> '') {
            $position[0] = (string) $txtparams[3];
        }

        // Vertical align: top/center/bottom
        if (isset($this->params['vpos']) && $this->params['vpos'] <> '') {
            $position[1] = (string) $this->params['vpos'];
        }
        if (isset($txtparams[4]) && $txtparams[4] <> '') {
            $position[1] = (string) $txtparams[4];
        }

        // Default Padding
        $padding = [0, 30];

        // Padding
        if (isset($this->params['padding_x']) && $this->params['padding_x'] <> '') {
            $padding[0] = (int) $this->params['padding_x'];
        }
        if (isset($txtparams[5]) && $txtparams[5] <> '') {
            $padding[0] = (int) $txtparams[5];
        }

        if (isset($this->params['padding_y']) && $this->params['padding_y'] <> '') {
            $padding[1] = (int) $this->params['padding_y'];
        }
        if (isset($txtparams[6]) && $txtparams[6] <> '') {
            $padding[1] = (int) $txtparams[6];
        }

        // Antialiasing
        $antialiasing = 1;
        if (isset($this->params['antialiasing']) && $this->params['antialiasing'] <> '') {
            $antialiasing = (int) $this->params['antialiasing'];
        }
        if (isset($txtparams[7]) && $txtparams[7] <> '') {
            $antialiasing = (int) $txtparams[7];
        }

        // ShadowColor
        $shadowcolor = [0, 0, 0];
        $shadowalpha = 0;
        $outputshadow = false;
        if (isset($this->params['shadowcolor']) && $this->params['shadowcolor'] <> '') {
            $rgba = $this->hexToRgb($this->params['shadowcolor']);
            $shadowcolor[0] = (int) $rgba['r'];
            $shadowcolor[1] = (int) $rgba['g'];
            $shadowcolor[2] = (int) $rgba['b'];
            $shadowalpha = (int) $rgba['a'];
            $outputshadow = true;
        }
        if (isset($txtparams[8]) && $txtparams[8] <> '') {
            $rgba = $this->hexToRgb($txtparams[8]);
            $shadowcolor[0] = (int) $rgba['r'];
            $shadowcolor[1] = (int) $rgba['g'];
            $shadowcolor[2] = (int) $rgba['b'];
            $shadowalpha = (int) $rgba['a'];
            $outputshadow = true;
        }

        // BGColor
        $bgcolor = [0, 0, 0];
        $bgalpha = 0;
        $outputbg = false;
        if (isset($this->params['bgcolor']) && $this->params['bgcolor'] <> '') {
            $rgba = $this->hexToRgb($this->params['bgcolor']);
            $bgcolor[0] = (int) $rgba['r'];
            $bgcolor[1] = (int) $rgba['g'];
            $bgcolor[2] = (int) $rgba['b'];
            $bgalpha = (int) $rgba['a'];
            $outputbg = true;
        }
        if (isset($txtparams[9]) && $txtparams[9] <> '') {
            $rgba = $this->hexToRgb($txtparams[9]);
            $bgcolor[0] = (int) $rgba['r'];
            $bgcolor[1] = (int) $rgba['g'];
            $bgcolor[2] = (int) $rgba['b'];
            $bgalpha = (int) $rgba['a'];
            $outputbg = true;
        }

        // BG Padding
        $bgpadding = 0;
        if (isset($this->params['bgpadding']) && $this->params['bgpadding'] <> '') {
            $bgpadding = (int) $this->params['bgpadding'] * 2;
        }
        if (isset($txtparams[10]) && $txtparams[10] <> '') {
            $bgpadding = (int) $txtparams[10] * 2;
        }

        // Text angle
        $text_angle = 0;
        if (isset($this->params['angle']) && $this->params['angle'] <> '') {
            $text_angle = (int) $this->params['angle'];
        }
        if (isset($txtparams[10]) && $txtparams[11] <> '') {
            $text_angle = (int) $txtparams[11];
        }

        // -------------------------------------- /CONFIG

        $this->media->asImage();
        $sourceImage = $this->media->getImage();

        // Keep transparency (for GIF, PNG & WebP)
        $this->keepTransparent($sourceImage);

        // Reset text-angle when background-color is used, save angle for rotate
        if ($outputbg) {
            $rotate_angle = $text_angle;
            $text_angle = 0;
        }

        // Calculate the textbox
        $the_box = $this->calculateTextBox($fontSize, $text_angle, $fontFile, $text);
        if ($the_box === false) {
            return;
        }
        if ($outputshadow) {
            $the_box['width'] +=1;
            $the_box['height'] +=1;
        }
        $the_tempbox = $the_box;

        // Antialiasing, 0 = no antialias, 1 = default
        $scale = 1;
        if ($antialiasing > 1) {
            $scale = $antialiasing;
            $the_tempbox = $this->calculateTextBox($fontSize * $scale, $text_angle, $fontFile, $text);
            if ($the_tempbox === false) {
                return;
            }
            if ($outputshadow) {
                $the_tempbox['width'] += (1*$scale);
                $the_tempbox['height'] += (1*$scale);
            }
        }

        // Set blending mode
        imagealphablending($sourceImage, true);

        // Create transparent temp image
        $imgWidth = $the_tempbox['width'] + (($bgpadding*2) * $scale);
        $imgHeight = $the_tempbox['height'] + (($bgpadding*2) * $scale);

        $gdTemp = imagecreatetruecolor($imgWidth, $imgHeight);
        imagefill($gdTemp, 0, 0, imagecolortransparent($gdTemp));

        // Background-Color
        if ($outputbg) {
            $col = imagecolorallocatealpha($gdTemp, $bgcolor[0], $bgcolor[1], $bgcolor[2], $bgalpha);
            imagefilledrectangle(
                $gdTemp,
                0,
                0,
                $imgWidth,
                $imgHeight,
                $col
            );
        }

        // Write text shadow
        if ($outputshadow) {
            $col = imagecolorallocatealpha($gdTemp, $shadowcolor[0], $shadowcolor[1], $shadowcolor[2], $shadowalpha);
            if ($antialiasing === 0) {
                $col = -$col;
            }
            imagettftext(
                $gdTemp,
                $fontSize * $scale,
                $text_angle,
                ($the_tempbox['left'] + ($imgWidth / 2) - ($the_tempbox['width'] / 2) + 1),
                ($the_tempbox['top'] + ($imgHeight / 2) - ($the_tempbox['height'] / 2) + 1),
                $col,
                $fontFile,
                $text
            );
        }

        // Write text
        $col = imagecolorallocatealpha($gdTemp, $color[0], $color[1], $color[2], $alpha);
        if ($antialiasing === 0) {
            $col = -$col;
        }
        imagettftext(
            $gdTemp,
            $fontSize * $scale,
            $text_angle,
            ($the_tempbox['left'] + ($imgWidth / 2) - ($the_tempbox['width'] / 2)),
            ($the_tempbox['top'] + ($imgHeight / 2) - ($the_tempbox['height'] / 2)),
            $col,
            $fontFile,
            $text
        );

        // Rotate the image when background-color for text is used
        if ($outputbg) {
            imagesetinterpolation($gdTemp, IMG_BILINEAR_FIXED);
            $gdTemp = imagerotate($gdTemp, $rotate_angle, imageColorAllocateAlpha($gdTemp, 0, 0, 0, 127));
            // get new Image-Size
            $imgWidth = imagesx($gdTemp);
            $imgHeight = imagesy($gdTemp);
            $the_box['width'] = $imgWidth;
            $the_box['height'] = $imgHeight;
            $bgpadding = 0;
        }

        // Source-Image-Dimensions
        $sourceImageWidth = $this->media->getWidth();
        $sourceImageHeight = $this->media->getHeight();

        // Horizontal position for the text
        switch ($position[0]) {
            case 'left':
                $dstX = 0;
                break;
            case 'center':
                $dstX = (int) (($sourceImageWidth - $the_box['width'] - $bgpadding) / 2);
                break;
            case 'right':
            default:
                $dstX = (int) ($sourceImageWidth - $the_box['width'] - $bgpadding*2);
        }

        // Vertical Position for the text
        switch ($position[1]) {
            case 'top':
                $dstY = 0;
                break;
            case 'middle':
                $dstY = (int) (($sourceImageHeight - $the_box['height'] - $bgpadding) / 2);
                break;
            case 'bottom':
            default:
                $dstY = (int) ($sourceImageHeight - $the_box['height'] - $bgpadding*2);
        }

        // Copy image / scaled image / rotated image to source
        imagecopyresampled(
            $sourceImage,
            $gdTemp,
            $dstX + $padding[0],
            $dstY + $padding[1],
            0,
            0,
            $the_box['width'] + ($bgpadding*2),
            $the_box['height'] + ($bgpadding*2),
            $imgWidth,
            $imgHeight
        );

        // Free memory
        imagedestroy($gdTemp);

        $this->media->setImage($sourceImage);
    }

    /**
     * Get box size
     * @param int $font_size
     * @param int $font_angle
     * @param string $font_file
     * @param string $text
     * @return array left,top,with,height
     */
    // calculateTextBox from https://www.php.net/manual/de/function.imagettfbbox.php#97357
    function calculateTextBox($font_size, $font_angle, $font_file, $text) {
        $box = imagettfbbox($font_size, $font_angle, $font_file, $text);
        if(!$box)
            return false;

        $min_x = min(array($box[0], $box[2], $box[4], $box[6]));
        $max_x = max(array($box[0], $box[2], $box[4], $box[6]));
        $min_y = min(array($box[1], $box[3], $box[5], $box[7]));
        $max_y = max(array($box[1], $box[3], $box[5], $box[7]));
        $width = ($max_x - $min_x);
        $height = ($max_y - $min_y);
        $left = abs($min_x) + $width;
        $top = abs($min_y) + $height;

        // to calculate the exact bounding box i write the text in a large image
        $img = @imagecreatetruecolor($width << 2, $height << 2);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $black);

        // for sure the text is completely in the image!
        imagettftext($img, $font_size, $font_angle, $left, $top, $white, $font_file, $text);

        // start scanning (0=> black => empty)
        $rleft = $w4 = $width<<2;
        $rright = 0;
        $rbottom   = 0;
        $rtop = $h4 = $height<<2;
        for ($x = 0; $x < $w4; $x++) {
            for ($y = 0; $y < $h4; $y++) {
                if (imagecolorat($img, $x, $y)) {
                    $rleft = min($rleft, $x);
                    $rright = max($rright, $x);
                    $rtop = min($rtop, $y);
                    $rbottom = max($rbottom, $y);
                }
            }
        }

        // destroy img and serve the result
        imagedestroy($img);
        return array(
            'left' => $left - $rleft,
            'top' => $top - $rtop,
            'width' => $rright - $rleft + 1,
            'height' => $rbottom - $rtop + 1
        );
    }

    /**
     * Get value from meta field in mediapool
     * @param string $field Name of field
     * @return string Value of field
     */
    public function getMeta($field = 'title')
    {
        $mediaName = $this->media->getMediaFilename();
        $media = rex_media::get($mediaName);
        return $media->getValue($field);
    }

    /**
     * Get list of meta fields in mediapool plus 'input'
     * @return array
     */
    public function getMetaFields() {

        if (rex_addon::get('metainfo')->isAvailable()) {
            $sql = rex_sql::factory();
            $sql->setQuery('SELECT name FROM ' . rex::getTablePrefix() . 'metainfo_field WHERE `name` LIKE "med_%" ORDER BY priority');
            $result = $sql->getArray();
            $list = array_merge(['input', 'title'], array_column($result, 'name'));
            return $list;
        }

        return ['input'];
    }

    /**
     * Get RGB from hex-String, RGBA from comma-separated String
     * @param string $hex
     * @return array
     */
    function hexToRgb($hex) {

        if (strpos($hex, '#') !== false) {
            $hex = str_replace('#', '', $hex);
            $length   = strlen($hex);
            $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
            $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
            $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
            $rgb['a'] = 0;
        } else {
            $rgba = explode(',', $hex);
            $rgb['r'] = isset($rgba[0]) ? $rgba[0] : 0;
            $rgb['g'] = isset($rgba[1]) ? $rgba[1] : 0;
            $rgb['b'] = isset($rgba[2]) ? $rgba[2] : 0;
            $rgb['a'] = isset($rgba[3]) ? $rgba[3] : 0;
        }

        return $rgb;
    }

    /**
     * Generate form input fields in Redaxo backend
     * @return array
     */
    public function getParams()
    {
        $params =  [
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_text'),
                'name' => 'text',
                'type' => 'string',
                'default' => 'Hello World!',
                'attributes' => [
                    'required' => 'required'
                ],
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_source'),
                'name' => 'text_source',
                'type' => 'select',
                'options' => static::getMetaFields(),
                'default' => 'custom',
                'notice' => rex_i18n::msg('media_manager_effect_insert_text_source_hint'),
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_font_size'),
                'name' => 'font_size',
                'type' => 'int',
                'default' => 30,
                'attributes' => [
                    'required' => 'required',
                    'pattern' => '[0-9]+',
                ],
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_font_file'),
                'name' => 'font_file',
                'type' => 'media',
                'default' => 'oswald-bold.ttf',
                'attributes' => [
                    'required' => 'required',
                ],
                'notice' => rex_i18n::msg('media_manager_effect_insert_text_font_file_info'),
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_color'),
                'name' => 'color',
                'type' => 'string',
                'default' => '',
                'notice' => rex_i18n::msg('media_manager_effect_insert_text_color_info'),
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_hpos'),
                'name' => 'hpos',
                'type' => 'select',
                'options' => ['left', 'center', 'right'],
                'default' => 'center',
             ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_vpos'),
                'name' => 'vpos',
                'type' => 'select',
                'options' => ['top', 'middle', 'bottom'],
                'default' => 'top',
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_padding_x'),
                'name' => 'padding_x',
                'type' => 'int',
                'default' => '0',
                'attributes' => [
                    'pattern' => '-?[0-9]+'
                ],
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_padding_y'),
                'name' => 'padding_y',
                'type' => 'int',
                'default' => '30',
                'attributes' => [
                    'pattern' => '-?[0-9]+'
                ],
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_antialiasing'),
                'name' => 'antialiasing',
                'type' => 'select',
                'options' => range(0, 5),
                'default' => 1,
                'notice' => rex_i18n::msg('media_manager_effect_insert_text_antialiasing_info'),
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_shadowcolor'),
                'name' => 'shadowcolor',
                'type' => 'string',
                'default' => '',
                'notice' => rex_i18n::msg('media_manager_effect_insert_text_shadowcolor_info'),
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_bgcolor'),
                'name' => 'bgcolor',
                'type' => 'string',
                'default' => '',
                'notice' => rex_i18n::msg('media_manager_effect_insert_text_bgcolor_info'),
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_bgpadding'),
                'name' => 'bgpadding',
                'type' => 'int',
                'default' => '',
                'attributes' => [
                    'pattern' => '[0-9]+'
                ],
                'notice' => rex_i18n::msg('media_manager_effect_insert_text_bgpadding_info'),
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_insert_text_angle'),
                'name' => 'angle',
                'type' => 'int',
                'default' => '',
                'attributes' => [
                    'pattern' => '-?[0-9]+'
                ],
                'notice' => rex_i18n::msg('media_manager_effect_insert_text_angle_info'),
            ],
        ];

        return $params;
    }

    /**
     * Get name of plugin
     * @return string
     */
    public function getName()
    {
        return rex_i18n::msg('media_manager_effect_insert_text_name');
    }
}
