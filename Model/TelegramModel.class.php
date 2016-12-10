<?php
    class TelegramModel extends FLModel {
        private $ret;
        
        public function __construct ($token = NULL) {
        	$this->token = $token;
        	parent::__construct ();
        }
        private function fetch ($url, $postdata = null) {
            // 访问
    		$ch = curl_init ();
    		curl_setopt ($ch, CURLOPT_URL, $url);
    		if (!is_null ($postdata)) {
    			curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query ($postdata));
    		}
    		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
    		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
    		$re = curl_exec ($ch);
    		curl_close ($ch);
    		
    		return $re;
    	}
    	protected function callMethod ($method, $param = array (), $detection = true) {
    	    // 初始化变量
    	    if ($this->token === NULL) {
            	$url = 'https://api.telegram.org/bot' . TOKEN . '/' . $method;
            } else {
            	$url = 'https://api.telegram.org/bot' . $this->token . '/' . $method;
            }
            
            // 访问网页
            $ret = json_decode ($this->fetch ($url, $param), true);
            
            // 分析结果
            if ($ret['ok'] == false && $detection == true) {
                $errorModel = new ErrorModel;
                $errorModel->sendError ('-1001078722237', '尝试调用 ' . $method . " 时出现问题，参数表如下：\n" . print_r ($param, true) . "\n\n返回结果：\n" . print_r ($ret, true));
            }
            
            // 返回
            return $ret;
    	}
    	public function setWebhook ($newurl) {
            $this->ret = $this->callMethod ('setWebhook', [
                'url' => $newurl
            ], false);
            return $this->ret;
        }
        public function sendMessage ($chat_id, $text, $reply_to_message_id = NULL, $reply_markup = array (), $parse_mode = 'HTML') {
            $this->ret = $this->callMethod ('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $text,
                'reply_to_message_id' => $reply_to_message_id,
                'parse_mode' => $parse_mode,
                'reply_markup' => $reply_markup
            ]);
            return $this->ret['result']['message_id'];
        }
        public function editMessage ($chat_id, $message_id, $text, $reply_markup = array (), $parse_mode = 'HTML') {
            $this->ret = $this->callMethod ('editMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $text,
                'parse_mode' => $parse_mode,
                'reply_markup' => $reply_markup
            ]);
            return $this->ret['result']['message_id'];
        }
        public function sendPhoto ($chat_id, $photo, $caption = '', $reply_to_message_id = NULL, $reply_markup = array ()) {
            $this->ret = $this->callMethod ('sendPhoto', [
                'chat_id' => $chat_id,
                'photo' => $photo,
                'caption' => $caption,
                'reply_to_message_id' => $reply_to_message_id,
                'reply_markup' => $reply_markup
            ]);
            return $this->ret['result']['message_id'];
        }
        public function sendAudio ($chat_id, $audio, $caption = '', $reply_to_message_id = NULL, $reply_markup = array ()) {
            $this->ret = $this->callMethod ('sendAudio', [
                'chat_id' => $chat_id,
                'audio' => $audio,
                'caption' => $caption,
                'reply_to_message_id' => $reply_to_message_id,
                'reply_markup' => $reply_markup
            ]);
            return $this->ret['result']['message_id'];
        }
        public function getChatAdmin ($chat_id) {
            $this->ret = $this->callMethod ('getChatAdministrators', [
                'chat_id' => $chat_id
            ]);
            return $this->ret['result'];
        }
        public function getMe () {
            $this->ret = $this->callMethod ('getMe', [
            ], false);
            return $this->ret;
        }
        public function isAdmin ($chat_id, $user_id) {
            $ret = false;
            $adminList = $this->getChatAdmin ($chat_id);
            foreach ($adminList as $adminList_d) {
                if ($adminList_d['user']['id'] == $user_id) {
                    $ret = true;
                    break;
                }
            }
            return $ret;
        }
        public function getReturn () {
            return $this->ret;
        }
        public function error () {
            $this->callMethod ('sendMessage');
        } 
    }