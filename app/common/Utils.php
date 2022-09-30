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
        $str = $this->trimAll($str);
        $str = $this->filterHtml($str);
        $str = $this->ch2en($str);
        $str = $this->filterOther($str);
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
        $str = strip_tags($str,"<img>");

        $RegExp = '/<img.*?>/';
        $res = preg_match_all($RegExp,$str,$result);
        if ($res) {
            $imgArray = $result[0];
        } else {
            $imgArray = array();
        }

        $RegExp = '/src=[\'\"](.*?)[\'\"]/';
        $res = preg_match_all($RegExp,$str,$result);
        if ($res) {
            $imgUrlArray = $result[1];
        } else {
            $imgUrlArray = array();
        }

        $str = str_replace($imgArray,$imgUrlArray,$str);
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
        $oldChar = array(" ", "　", "\t", "\n", "\r");
        $newChar = array("", "", "", "", "");
        $str = str_replace($oldChar,$newChar,$str);
        $str =  preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$str);
        return $str;
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

        $str = str_replace($chChar,$enChar,$str);

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
        $str = preg_replace('/\[填空\(\d\)]/','',$str);
        $str = preg_replace('/\[填空\d]/','',$str);
        $str = preg_replace('/\[填空]/','',$str);
        $str = preg_replace('/^\d+\s/','',$str);
        $str = preg_replace('/\[填空\d]/','',$str);
        $str = preg_replace('/\+/','+',$str);
        $str = preg_replace('/\-/','-',$str);
        $str = preg_replace('/\=/','=',$str);
        $str = preg_replace('/\&nbsp\;/','',$str);
        $str = preg_replace('/\&ensp\;/','',$str);
        $str = preg_replace('/\&emsp\;/','',$str);
        $str = preg_replace('/\&lt\;/','<',$str);
        $str = preg_replace('/\&gt\;/','>',$str);
        $str = preg_replace('/\&amp\;/','&',$str);
        $str = preg_replace('/\&quot\;/','"',$str);
        $str = preg_replace('/\&times\;/','×',$str);
        $str = preg_replace('/\&divide\;/','÷',$str);
        $str = preg_replace('/\&ldquo\;/','“',$str);
        $str = preg_replace('/\&rdquo\;/','”',$str);
        $str = preg_replace('/\&\#39\;/','\'',$str);
        $str = preg_replace('/\&rsquo\;/','’',$str);
        $str = preg_replace('/\&mdash\;/','—',$str);
        $str = preg_replace('/\&ndash\;/','–',$str);
        $str = str_replace('\u00a0','',$str);
        $str = str_replace('\u2003','',$str);
        $str = str_replace('\u2002','',$str);
        $str = str_replace('\u0020','',$str);
        $str = preg_replace('/\s+/','',$str);
        $str = preg_replace('/<![\S\s]*?>/','',$str);
        $str = preg_replace('/\(\d+\.\d+分\)$/','',$str);
        $str = preg_replace('/·/','',$str);
        $str = preg_replace('/^([.;?,\'":])/','',$str);
        $str = preg_replace('/([.;?,\'":])$/','',$str);
        $str = preg_replace('/题型说明[:：]请输入题型说明/','',$str);
        $str = preg_replace('/题型说明[:：]每题\d+分[,，]共\d+分/','',$str);
        return $str;
    }
}