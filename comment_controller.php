<?php

/**
 * Comment_controller class
 *
 * @package munkireport
 * @author AvB
 **/
class Comment_controller extends Module_controller
{
    /**
     * Create a comment
     *
     **/
    public function save()
    {
        $out = array();

        // Sanitize
        $serial_number = post('serial_number');
        $section = post('section');
        $text = post('text');
        $html = post('html');
        if($html){
            $html = $this->_filter_html($html);
        }

        if ($serial_number and $section and $text) {
            if (authorized_for_serial($serial_number)) {
                $comment = Comment_model::updateOrCreate(
                    [
                        'serial_number' => $serial_number,
                        'section' => $section,
                    ],
                    [
                        'text' => $text,
                        'html' => $html,
                        'user' => $_SESSION['user'],
                        'timestamp' => time(),
                    ]
                );
                $out['status'] = 'saved';
            } else {
                $out['status'] = 'error';
                $out['msg'] = 'Not authorized for this serial';
            }
        } else {
            $out['status'] = 'error';
            $out['msg'] = 'Missing data';
        }

        $obj = new View();
        $obj->view('json', array('msg' => $out));
    }

    /**
     * Retrieve data in json format
     *
     **/
    public function retrieve($serial_number = '', $section = '')
    {
        $out = [];
        $where[] = ['serial_number', $serial_number];
        if($section){
            $where[] = ['section', $section];
            $comment = Comment_model::where($where)->first();
            if ($comment) {
                $out = $comment;
            }
        }else {
            $comment = Comment_model::where($where)->get();
            if($comment){
                $out = $comment->toArray();
            }
        }

        $obj = new View();
        $obj->view('json', array('msg' => $out));
    }

    /**
     * Update comment
     *
     **/
    public function update()
    {
    }

    /**
     * Delete comment
     *
     **/
    public function delete()
    {
    }

    private function _filter_html($html)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD);
        $tags_to_remove = array('script','style','iframe','link');
        foreach($tags_to_remove as $tag){
            $element = $dom->getElementsByTagName($tag);
            foreach($element  as $item){
                $item->parentNode->removeChild($item);
            }
        }
        return $dom->saveHTML();
    }
} // END class Comment_controller
