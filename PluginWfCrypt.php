<?php
class PluginWfCrypt{
  
  function __construct($buto = false) {
    if($buto){
    }
  }
  /**
  */
  private $settings = null;
  /**
   <p>Init method.</p>
   */
  private function init(){
    if(!wfUser::hasRole("webadmin")){
      exit('Role webadmin is required!');
    }
    wfArray::set($GLOBALS, 'sys/layout_path', '/plugin/wf/crypt/layout');
    wfPlugin::includeonce('wf/array');
    $this->settings = new PluginWfArray(wfArray::get($GLOBALS, 'sys/settings/plugin_modules/'.wfArray::get($GLOBALS, 'sys/class').'/settings'));
    /**
     * Handle mysql param if string to yml file.
     */
    //$this->settings->set('mysql', wfSettings::getSettingsFromYmlString($this->settings->get('mysql')));
  }
  
  public function page_desktop(){
    $this->init();
    $page = $this->getYml('page/desktop.yml');
    $page = wfDocument::insertAdminLayout($this->settings, 1, $page);
    wfDocument::mergeLayout($page->get());
  }
  public function page_encrypt(){
    $this->init();
    $page = $this->getYml('page/encrypt.yml');
    $page = wfDocument::insertAdminLayout($this->settings, 1, $page);
    wfDocument::mergeLayout($page->get());
  }
  public function page_decrypt(){
    $this->init();
    $page = $this->getYml('page/decrypt.yml');
    $page = wfDocument::insertAdminLayout($this->settings, 1, $page);
    wfDocument::mergeLayout($page->get());
  }
  public function page_do_encrypt(){
    /**
     * Vars.
     */
    $rows = preg_split("/\r\n/", wfRequest::get('text'));
    $crypt_key = wfRequest::get('key');
    $omit = wfRequest::get('omit');
    /**
     * Set data.
     */
    $data = array();
    foreach ($rows as $key => $value) {
      $data[] = array('text' => $value, 'encrypt' => wfCrypt::encrypt($value, $crypt_key));
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
    /**
     * Vars.
     */
    $rows = preg_split("/\r\n/", wfRequest::get('text'));
    $crypt_key = wfRequest::get('key');
    $omit = wfRequest::get('omit');
    /**
     * Set data.
     */
    $data = array();
    foreach ($rows as $key => $value) {
      if(strlen($value) < 22){
        $data[] = array('text' => $value, 'decrypt' => '_size_less_then_22_');
      }else{
        $data[] = array('text' => $value, 'decrypt' => wfCrypt::decrypt($value, $crypt_key));
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