<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.3.6
 * @copyright Copyright (C) 2025 Mirasvit (https://mirasvit.com/)
 */



class Mirasvit_Ddeboer_Imap_Charset
{
    /**
     * @var array
     */
    private $charsetAlias = [
        'ascii'                    => 'us-ascii',
        'us-ascii'                 => 'us-ascii',
        'ansi_x3.4-1968'           => 'us-ascii',
        '646'                      => 'us-ascii',
        'iso-8859-1'               => 'ISO-8859-1',
        'iso-8859-2'               => 'ISO-8859-2',
        'iso-8859-3'               => 'ISO-8859-3',
        'iso-8859-4'               => 'ISO-8859-4',
        'iso-8859-5'               => 'ISO-8859-5',
        'iso-8859-6'               => 'ISO-8859-6',
        'iso-8859-6-i'             => 'ISO-8859-6-I',
        'iso-8859-6-e'             => 'ISO-8859-6-E',
        'iso-8859-7'               => 'ISO-8859-7',
        'iso-8859-8'               => 'ISO-8859-8',
        'iso-8859-8-i'             => 'ISO-8859-8-I',
        'iso-8859-8-e'             => 'ISO-8859-8-E',
        'iso-8859-9'               => 'ISO-8859-9',
        'iso-8859-10'              => 'ISO-8859-10',
        'iso-8859-11'              => 'ISO-8859-11',
        'iso-8859-13'              => 'ISO-8859-13',
        'iso-8859-14'              => 'ISO-8859-14',
        'iso-8859-15'              => 'ISO-8859-15',
        'iso-8859-16'              => 'ISO-8859-16',
        'iso-ir-111'               => 'ISO-IR-111',
        'iso-2022-cn'              => 'ISO-2022-CN',
        'iso-2022-cn-ext'          => 'ISO-2022-CN',
        'iso-2022-kr'              => 'ISO-2022-KR',
        'iso-2022-jp'              => 'ISO-2022-JP',
        'utf-16be'                 => 'UTF-16BE',
        'utf-16le'                 => 'UTF-16LE',
        'utf-16'                   => 'UTF-16',
        'windows-1250'             => 'windows-1250',
        'windows-1251'             => 'windows-1251',
        'windows-1252'             => 'windows-1252',
        'windows-1253'             => 'windows-1253',
        'windows-1254'             => 'windows-1254',
        'windows-1255'             => 'windows-1255',
        'windows-1256'             => 'windows-1256',
        'windows-1257'             => 'windows-1257',
        'windows-1258'             => 'windows-1258',
        'ibm866'                   => 'IBM866',
        'ibm850'                   => 'IBM850',
        'ibm852'                   => 'IBM852',
        'ibm855'                   => 'IBM855',
        'ibm857'                   => 'IBM857',
        'ibm862'                   => 'IBM862',
        'ibm864'                   => 'IBM864',
        'utf-8'                    => 'UTF-8',
        'utf-7'                    => 'UTF-7',
        'shift_jis'                => 'Shift_JIS',
        'big5'                     => 'Big5',
        'euc-jp'                   => 'EUC-JP',
        'euc-kr'                   => 'EUC-KR',
        'gb2312'                   => 'GB2312',
        'gb18030'                  => 'gb18030',
        'viscii'                   => 'VISCII',
        'koi8-r'                   => 'KOI8-R',
        'koi8_r'                   => 'KOI8-R',
        'cskoi8r'                  => 'KOI8-R',
        'koi'                      => 'KOI8-R',
        'koi8'                     => 'KOI8-R',
        'koi8-u'                   => 'KOI8-U',
        'tis-620'                  => 'TIS-620',
        't.61-8bit'                => 'T.61-8bit',
        'hz-gb-2312'               => 'HZ-GB-2312',
        'big5-hkscs'               => 'Big5-HKSCS',
        'gbk'                      => 'gbk',
        'cns11643'                 => 'x-euc-tw',
        'x-imap4-modified-utf7'    => 'x-imap4-modified-utf7',
        'x-euc-tw'                 => 'x-euc-tw',
        'x-mac-ce'                 => 'x-mac-ce',
        'x-mac-turkish'            => 'x-mac-turkish',
        'x-mac-greek'              => 'x-mac-greek',
        'x-mac-icelandic'          => 'x-mac-icelandic',
        'x-mac-croatian'           => 'x-mac-croatian',
        'x-mac-romanian'           => 'x-mac-romanian',
        'x-mac-cyrillic'           => 'x-mac-cyrillic',
        'x-mac-ukrainian'          => 'x-mac-cyrillic',
        'x-mac-hebrew'             => 'x-O    mac-hebrew',
        'x-mac-arabic'             => 'x-mac-arabic',
        'x-mac-farsi'              => 'x-mac-farsi',
        'x-mac-devanagari'         => 'x-mac-devanagari',
        'x-mac-gujarati'           => 'x-mac-gujarati',
        'x-mac-gurmukhi'           => 'x-mac-gurmukhi',
        'armscii-8'                => 'armscii-8',
        'x-viet-tcvn5712'          => 'x-viet-tcvn5712',
        'x-viet-vps'               => 'x-viet-vps',
        'iso-10646-ucs-2'          => 'UTF-16BE',
        'x-iso-10646-ucs-2-be'     => 'UTF-16BE',
        'x-iso-10646-ucs-2-le'     => 'UTF-16LE',
        'x-user-defined'           => 'x-user-defined',
        'x-johab'                  => 'x-johab',
        'latin1'                   => 'ISO-8859-1',
        'iso_8859-1'               => 'ISO-8859-1',
        'iso8859-1'                => 'ISO-8859-1',
        'iso8859-2'                => 'ISO-8859-2',
        'iso8859-3'                => 'ISO-8859-3',
        'iso8859-4'                => 'ISO-8859-4',
        'iso8859-5'                => 'ISO-8859-5',
        'iso8859-6'                => 'ISO-8859-6',
        'iso8859-7'                => 'ISO-8859-7',
        'iso8859-8'                => 'ISO-8859-8',
        'iso8859-9'                => 'ISO-8859-9',
        'iso8859-10'               => 'ISO-8859-10',
        'iso8859-11'               => 'ISO-8859-11',
        'iso8859-13'               => 'ISO-8859-13',
        'iso8859-14'               => 'ISO-8859-14',
        'iso8859-15'               => 'ISO-8859-15',
        'iso_8859-1:1987'          => 'ISO-8859-1',
        'iso-ir-100'               => 'ISO-8859-1',
        'l1'                       => 'ISO-8859-1',
        'ibm819'                   => 'ISO-8859-1',
        'cp819'                    => 'ISO-8859-1',
        'csisolatin1'              => 'ISO-8859-1',
        'latin2'                   => 'ISO-8859-2',
        'iso_8859-2'               => 'ISO-8859-2',
        'iso_8859-2:1987'          => 'ISO-8859-2',
        'iso-ir-101'               => 'ISO-8859-2',
        'l2'                       => 'ISO-8859-2',
        'csisolatin2'              => 'ISO-8859-2',
        'latin3'                   => 'ISO-8859-3',
        'iso_8859-3'               => 'ISO-8859-3',
        'iso_8859-3:1988'          => 'ISO-8859-3',
        'iso-ir-109'               => 'ISO-8859-3',
        'l3'                       => 'ISO-8859-3',
        'csisolatin3'              => 'ISO-8859-3',
        'latin4'                   => 'ISO-8859-4',
        'iso_8859-4'               => 'ISO-8859-4',
        'iso_8859-4:1988'          => 'ISO-8859-4',
        'iso-ir-110'               => 'ISO-8859-4',
        'l4'                       => 'ISO-8859-4',
        'csisolatin4'              => 'ISO-8859-4',
        'cyrillic'                 => 'ISO-8859-5',
        'iso_8859-5'               => 'ISO-8859-5',
        'iso_8859-5:1988'          => 'ISO-8859-5',
        'iso-ir-144'               => 'ISO-8859-5',
        'csisolatincyrillic'       => 'ISO-8859-5',
        'arabic'                   => 'ISO-8859-6',
        'iso_8859-6'               => 'ISO-8859-6',
        'iso_8859-6:1987'          => 'ISO-8859-6',
        'iso-ir-127'               => 'ISO-8859-6',
        'ecma-114'                 => 'ISO-8859-6',
        'asmo-708'                 => 'ISO-8859-6',
        'csisolatinarabic'         => 'ISO-8859-6',
        'csiso88596i'              => 'ISO-8859-6-I',
        'csiso88596e'              => 'ISO-8859-6-E',
        'greek'                    => 'ISO-8859-7',
        'greek8'                   => 'ISO-8859-7',
        'sun_eu_greek'             => 'ISO-8859-7',
        'iso_8859-7'               => 'ISO-8859-7',
        'iso_8859-7:1987'          => 'ISO-8859-7',
        'iso-ir-126'               => 'ISO-8859-7',
        'elot_928'                 => 'ISO-8859-7',
        'ecma-118'                 => 'ISO-8859-7',
        'csisolatingreek'          => 'ISO-8859-7',
        'hebrew'                   => 'ISO-8859-8',
        'iso_8859-8'               => 'ISO-8859-8',
        'visual'                   => 'ISO-8859-8',
        'iso_8859-8:1988'          => 'ISO-8859-8',
        'iso-ir-138'               => 'ISO-8859-8',
        'csisolatinhebrew'         => 'ISO-8859-8',
        'csiso88598i'              => 'ISO-8859-8-I',
        'iso-8859-8i'              => 'ISO-8859-8-I',
        'logical'                  => 'ISO-8859-8-I',
        'csiso88598e'              => 'ISO-8859-8-E',
        'latin5'                   => 'ISO-8859-9',
        'iso_8859-9'               => 'ISO-8859-9',
        'iso_8859-9:1989'          => 'ISO-8859-9',
        'iso-ir-148'               => 'ISO-8859-9',
        'l5'                       => 'ISO-8859-9',
        'csisolatin5'              => 'ISO-8859-9',
        'unicode-1-1-utf-8'        => 'UTF-8',
        'utf8'                     => 'UTF-8',
        'x-sjis'                   => 'Shift_JIS',
        'shift-jis'                => 'Shift_JIS',
        'ms_kanji'                 => 'Shift_JIS',
        'csshiftjis'               => 'Shift_JIS',
        'windows-31j'              => 'Shift_JIS',
        'cp932'                    => 'Shift_JIS',
        'sjis'                     => 'Shift_JIS',
        'cseucpkdfmtjapanese'      => 'EUC-JP',
        'x-euc-jp'                 => 'EUC-JP',
        'csiso2022jp'              => 'ISO-2022-JP',
        'iso-2022-jp-2'            => 'ISO-2022-JP',
        'csiso2022jp2'             => 'ISO-2022-JP',
        'csbig5'                   => 'Big5',
        'cn-big5'                  => 'Big5',
        'x-x-big5'                 => 'Big5',
        'zh_tw-big5'               => 'Big5',
        'cseuckr'                  => 'EUC-KR',
        'ks_c_5601-1987'           => 'EUC-KR',
        'iso-ir-149'               => 'EUC-KR',
        'ks_c_5601-1989'           => 'EUC-KR',
        'ksc_5601'                 => 'EUC-KR',
        'ksc5601'                  => 'EUC-KR',
        'korean'                   => 'EUC-KR',
        'csksc56011987'            => 'EUC-KR',
        '5601'                     => 'EUC-KR',
        'windows-949'              => 'EUC-KR',
        'gb_2312-80'               => 'GB2312',
        'iso-ir-58'                => 'GB2312',
        'chinese'                  => 'GB2312',
        'csiso58gb231280'          => 'GB2312',
        'csgb2312'                 => 'GB2312',
        'zh_cn.euc'                => 'GB2312',
        'gb_2312'                  => 'GB2312',
        'x-cp1250'                 => 'windows-1250',
        'x-cp1251'                 => 'windows-1251',
        'x-cp1252'                 => 'windows-1252',
        'x-cp1253'                 => 'windows-1253',
        'x-cp1254'                 => 'windows-1254',
        'x-cp1255'                 => 'windows-1255',
        'x-cp1256'                 => 'windows-1256',
        'x-cp1257'                 => 'windows-1257',
        'x-cp1258'                 => 'windows-1258',
        'windows-874'              => 'windows-874',
        'ibm874'                   => 'windows-874',
        'dos-874'                  => 'windows-874',
        'macintosh'                => 'macintosh',
        'x-mac-roman'              => 'macintosh',
        'mac'                      => 'macintosh',
        'csmacintosh'              => 'macintosh',
        'cp866'                    => 'IBM866',
        'cp-866'                   => 'IBM866',
        '866'                      => 'IBM866',
        'csibm866'                 => 'IBM866',
        'cp850'                    => 'IBM850',
        '850'                      => 'IBM850',
        'csibm850'                 => 'IBM850',
        'cp852'                    => 'IBM852',
        '852'                      => 'IBM852',
        'csibm852'                 => 'IBM852',
        'cp855'                    => 'IBM855',
        '855'                      => 'IBM855',
        'csibm855'                 => 'IBM855',
        'cp857'                    => 'IBM857',
        '857'                      => 'IBM857',
        'csibm857'                 => 'IBM857',
        'cp862'                    => 'IBM862',
        '862'                      => 'IBM862',
        'csibm862'                 => 'IBM862',
        'cp864'                    => 'IBM864',
        '864'                      => 'IBM864',
        'csibm864'                 => 'IBM864',
        'ibm-864'                  => 'IBM864',
        't.61'                     => 'T.61-8bit',
        'iso-ir-103'               => 'T.61-8bit',
        'csiso103t618bit'          => 'T.61-8bit',
        'x-unicode-2-0-utf-7'      => 'UTF-7',
        'unicode-2-0-utf-7'        => 'UTF-7',
        'unicode-1-1-utf-7'        => 'UTF-7',
        'csunicode11utf7'          => 'UTF-7',
        'csunicode'                => 'UTF-16BE',
        'csunicode11'              => 'UTF-16BE',
        'iso-10646-ucs-basic'      => 'UTF-16BE',
        'csunicodeascii'           => 'UTF-16BE',
        'iso-10646-unicode-latin1' => 'UTF-16BE',
        'csunicodelatin1'          => 'UTF-16BE',
        'iso-10646'                => 'UTF-16BE',
        'iso-10646-j-1'            => 'UTF-16BE',
        'latin6'                   => 'ISO-8859-10',
        'iso-ir-157'               => 'ISO-8859-10',
        'l6'                       => 'ISO-8859-10',
        'csisolatin6'              => 'ISO-8859-10',
        'iso_8859-15'              => 'ISO-8859-15',
        'csisolatin9'              => 'ISO-8859-15',
        'l9'                       => 'ISO-8859-15',
        'ecma-cyrillic'            => 'ISO-IR-111',
        'csiso111ecmacyrillic'     => 'ISO-IR-111',
        'csiso2022kr'              => 'ISO-2022-KR',
        'csviscii'                 => 'VISCII',
        'zh_tw-euc'                => 'x-euc-tw',
        'iso88591'                 => 'ISO-8859-1',
        'iso88592'                 => 'ISO-8859-2',
        'iso88593'                 => 'ISO-8859-3',
        'iso88594'                 => 'ISO-8859-4',
        'iso88595'                 => 'ISO-8859-5',
        'iso88596'                 => 'ISO-8859-6',
        'iso88597'                 => 'ISO-8859-7',
        'iso88598'                 => 'ISO-8859-8',
        'iso88599'                 => 'ISO-8859-9',
        'iso885910'                => 'ISO-8859-10',
        'iso885911'                => 'ISO-8859-11',
        'iso885912'                => 'ISO-8859-12',
        'iso885913'                => 'ISO-8859-13',
        'iso885914'                => 'ISO-8859-14',
        'iso885915'                => 'ISO-8859-15',
        'tis620'                   => 'TIS-620',
        'cp1250'                   => 'windows-1250',
        'cp1251'                   => 'windows-1251',
        'cp1252'                   => 'windows-1252',
        'cp1253'                   => 'windows-1253',
        'cp1254'                   => 'windows-1254',
        'cp1255'                   => 'windows-1255',
        'cp1256'                   => 'windows-1256',
        'cp1257'                   => 'windows-1257',
        'cp1258'                   => 'windows-1258',
        'x-gbk'                    => 'gbk',
        'windows-936'              => 'gbk',
        'ansi-1251'                => 'windows-1251',
    ];

    /**
     * @param string $charset
     * @return string
     */
    public function getCharset($charset)
    {
        $charset = strtolower($charset);
        $encodings = mb_list_encodings();

        if (in_array($charset, $encodings)) {
            return $charset;
        }

        return $this->getCharsetAlias($charset);
    }

    /**
     * @param string $charset
     * @return string
     */
    public function getCharsetAlias($charset)
    {
        $charset = strtolower($charset);

        return (isset($this->charsetAlias[$charset])) ? $this->charsetAlias[$charset] : $charset;
    }
}