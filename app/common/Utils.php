<?php

namespace app\common;

/**
 * 工具类
 *
 */
class Utils
{
    /**
     * 过滤字符串
     *
     * @param string $str
     * @return string
     */
    public function filterStr(string $str): string
    {
        $str = $this->filterHtml($str);
        $str = $this->trimAll($str);
        $str = $this->dbc2sbc($str);
        $str = $this->ch2en($str);
        $str = $this->filterOther($str);
        if (strlen($str) == 0) {
            $str = 'ThinkPHP';
        }
        return $str;
    }

    /**
     * 过滤html标签，替换img标签为src网址
     *
     * @param string $str
     * @return string
     */
    protected function filterHtml(string $str): string
    {
        $str = strip_tags($str, "<img>");

        $RegExp = '/(&lt;|<)img.*?(&gt;|>)/';
        $res = preg_match_all($RegExp, $str, $result);
        if ($res) {
            $imgArray = $result[0];
        } else {
            $imgArray = array();
        }

        $RegExp = '/src=[\'\"](.*?)[\'\"]/';
        $res = preg_match_all($RegExp, $str, $result);
        if ($res) {
            $imgUrlArray = $result[1];
        } else {
            $imgUrlArray = array();
        }

        $str = str_replace($imgArray, $imgUrlArray, $str);
        return $str;
    }

    /**
     * 删除所有空格
     *
     * @param string $str
     * @return string
     */
    protected function trimAll(string $str): string
    {
        /**
         * \u2003  
         * \u00a0  
         * \u2002  
         * \u0020
         **/
        $oldChar = array(" ", "　", "\t", "\n", "\r");
        $newChar = array("", "", "", "", "");
        $str = str_replace($oldChar, $newChar, $str);
        $str = preg_replace("/(\s|\&nbsp\;| | | | |　|\xc2\xa0)/", "", $str);
        return $str;
    }

    /**
     * 字符串全角->半角转换
     *
     * @param string $str 待转换的字符串
     * @return string 返回转换后的字符串
     */
    protected function dbc2sbc(string $str): string
    {
        // 全角
        $dbc = array(
            '０', '１', '２', '３', '４',
            '５', '６', '７', '８', '９',
            'Ａ', 'Ｂ', 'Ｃ', 'Ｄ', 'Ｅ',
            'Ｆ', 'Ｇ', 'Ｈ', 'Ｉ', 'Ｊ',
            'Ｋ', 'Ｌ', 'Ｍ', 'Ｎ', 'Ｏ',
            'Ｐ', 'Ｑ', 'Ｒ', 'Ｓ', 'Ｔ',
            'Ｕ', 'Ｖ', 'Ｗ', 'Ｘ', 'Ｙ',
            'Ｚ', 'ａ', 'ｂ', 'ｃ', 'ｄ',
            'ｅ', 'ｆ', 'ｇ', 'ｈ', 'ｉ',
            'ｊ', 'ｋ', 'ｌ', 'ｍ', 'ｎ',
            'ｏ', 'ｐ', 'ｑ', 'ｒ', 'ｓ',
            'ｔ', 'ｕ', 'ｖ', 'ｗ', 'ｘ',
            'ｙ', 'ｚ', '－', '　', '：',
            '．', '，', '／', '％', '＃',
            '！', '＠', '＆', '（', '）',
            '＜', '＞', '＂', '＇', '？',
            '［', '］', '｛', '｝', '＼',
            '｜', '＋', '＝', '＿', '＾',
            '￥', '￣', '｀'

        );

        //半角
        $sbc = array(
            '0', '1', '2', '3', '4',
            '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E',
            'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O',
            'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y',
            'Z', 'a', 'b', 'c', 'd',
            'e', 'f', 'g', 'h', 'i',
            'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'q', 'r', 's',
            't', 'u', 'v', 'w', 'x',
            'y', 'z', '-', ' ', ':',
            '.', ',', '/', '%', ' #',
            '!', '@', '&', '(', ')',
            '<', '>', '"', '\'', '?',
            '[', ']', '{', '}', '\\',
            '|', '+', '=', '_', '^',
            '￥', '~', '`'
        );


        //全角到半角
        return str_replace($dbc, $sbc, $str);
    }

    /**
     * 中文标点替换成英文标点
     *
     * @param string $str
     * @return string
     */
    protected function ch2en(string $str): string
    {

        //中文标点符号
        $chChar = array("！", "，", "。", "；", "《", "》", "（", "）", "？", "｛", "｝", "“", "：", "【", "】", "”", "‘", "’");
        //英文标点符号
        $enChar = array("!", ",", ".", ";", "<", ">", "(", ")", "?", "{", "}", "\"", ":", "{", "}", "\"", "'", "'");

        $str = str_replace($chChar, $enChar, $str);

        return $str;
    }

    /**
     * 过滤其他的内容
     *
     * @param string $str
     * @return string
     */
    protected function filterOther(string $str): string
    {
        $str = preg_replace('/\[填空\(\d\)]/', '', $str);
        $str = preg_replace('/\[填空\d]/', '', $str);
        $str = preg_replace('/\[填空]/', '', $str);
        $str = preg_replace('/^\d+\s/', '', $str);
        $str = preg_replace('/\[填空\d]/', '', $str);
        $str = preg_replace('/\+/', '+', $str);
        $str = preg_replace('/\-/', '-', $str);
        $str = preg_replace('/\=/', '=', $str);
        $str = preg_replace('/\&nbsp\;/', '', $str);
        $str = preg_replace('/\&ensp\;/', '', $str);
        $str = preg_replace('/\&emsp\;/', '', $str);
        $str = preg_replace('/\&lt\;/', '<', $str);
        $str = preg_replace('/\&gt\;/', '>', $str);
        $str = preg_replace('/\&amp\;/', '&', $str);
        $str = preg_replace('/\&quot\;/', '"', $str);
        $str = preg_replace('/\&times\;/', '×', $str);
        $str = preg_replace('/\&divide\;/', '÷', $str);
        $str = preg_replace('/\&ldquo\;/', '“', $str);
        $str = preg_replace('/\&rdquo\;/', '”', $str);
        $str = preg_replace('/\&\#39\;/', '\'', $str);
        $str = preg_replace('/\&rsquo\;/', '’', $str);
        $str = preg_replace('/\&mdash\;/', '—', $str);
        $str = preg_replace('/\&ndash\;/', '–', $str);
        $str = preg_replace('/\s+/', '', $str);
        $str = preg_replace('/<![\S\s]*?>/', '', $str);
        $str = preg_replace('/\(\d+\.\d+分\)$/', '', $str);
        $str = preg_replace('/\(\d+\.\d+\)$/', '', $str);
        $str = preg_replace('/·/', '', $str);
        $str = preg_replace('/(\(|\（)\s*(\)|\）)\s*$/', '', $str);
        $str = preg_replace('/^([.;?,\'":])/', '', $str);
        $str = preg_replace('/([.;?,\'":])$/', '', $str);
        $str = preg_replace('/^\{.*?题\}/', '', $str);
        $str = preg_replace('/^\{阅读理解\}/', '', $str);
        $str = preg_replace('/题型说明[:：]请输入题型说明/', '', $str);
        $str = preg_replace('/题型说明[:：]每题\d+分[,，]共\d+分/', '', $str);
        return $str;
    }
}