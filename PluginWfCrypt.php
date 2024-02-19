<?php
class PluginWfCrypt{
  private $crypt = null;
  function __construct($buto = false) {
    wfPlugin::includeonce('crypt/openssl');
    $this->crypt = new PluginCryptOpenssl();
    $this->crypt->data->set('passphrase', 'E0sjaFTC');
  }
  public function page_desktop(){
    $page = $this->getYml('page/desktop.yml');
    wfDocument::renderElement($page->get());
  }
  public function page_encrypt(){
    $page = $this->getYml('page/encrypt.yml');
    wfDocument::renderElement($page->get());
  }
  public function page_decrypt(){
    $page = $this->getYml('page/decrypt.yml');
    wfDocument::renderElement($page->get());
  }
  public function page_do_encrypt(){
    if(!wfRequest::get('text')){
      exit('Text is missing!');
    }
    /**
     * Vars.
     */
    $rows = preg_split("/\r\n/", wfRequest::get('text'));
    $crypt_key = wfRequest::get('key');
    $omit = wfRequest::get('omit');
    $method = wfRequest::get('method');
    /**
     * Set data.
     */
    $data = array();
    foreach ($rows as $key => $value) {
      if($method=='openssl'){
        $data[] = array('text' => $value, 'encrypt' => $this->crypt->encrypt($value, $crypt_key));
      }elseif($method=='mcrypt'){
        $data[] = array('text' => $value, 'encrypt' => wfCrypt::encrypt($value, $crypt_key));
      }
    }
    /**
     * Show result in textarea.
     */
    $innerHTML = null;
    foreach ($data as $key => $value) {
      if($omit){
        $innerHTML .= $value['encrypt']."\n";
      }else{
        $innerHTML .= $value['text']."\t".$value['encrypt']."\n";
      }
    }
    $textarea = wfDocument::createHtmlElementAsObject('textarea', $innerHTML, array('style' => 'width:100%;height:300px;'));
    wfDocument::renderElement(array($textarea->get()));
    /*
     * Exit.
     */
    exit;
  }
  public function page_do_decrypt(){
    if(!wfRequest::get('text')){
      exit('Text is missing!');
    }
    /**
     * Vars.
     */
    $rows = preg_split("/\r\n/", wfRequest::get('text'));
    $crypt_key = wfRequest::get('key');
    $omit = wfRequest::get('omit');
    $method = wfRequest::get('method');
    /**
     * Set data.
     */
    $data = array();
    foreach ($rows as $key => $value) {
      if($method=='openssl'){
        $data[] = array('text' => $value, 'decrypt' => $this->crypt->decrypt($value, $crypt_key));
      }elseif($method=='mcrypt'){
        if(wfPhpfunc::strlen($value) < 22){
          $data[] = array('text' => $value, 'decrypt' => '_size_less_then_22_');
        }else{
          $data[] = array('text' => $value, 'decrypt' => wfCrypt::decrypt($value, $crypt_key));
        }
      }
    }
    /**
     * Show result in textarea.
     */
    $innerHTML = null;
    foreach ($data as $key => $value) {
      if($omit){
        $innerHTML .= $value['decrypt']."\n";
      }else{
        $innerHTML .= $value['text']."\t".$value['decrypt']."\n";
      }
    }
    $textarea = wfDocument::createHtmlElementAsObject('textarea', $innerHTML, array('style' => 'width:100%;height:300px;'));
    wfDocument::renderElement(array($textarea->get()));
    /*
     * Exit.
     */
    exit;
  }
  private function getYml($file = 'element/_some_file.yml'){
    wfPlugin::includeonce('wf/yml');
    return new PluginWfYml('/plugin/wf/crypt/'.$file);
  }
}
