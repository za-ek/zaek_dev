<?php
if ( !in_array($this->conf()->get('client', 'ip'), $this->conf()->get('updates', 'allow_ip')) ) {
    throw new \zaek\kernel\CException('IP_NOT_ALLOWED ['.$this->conf()->get('client', 'ip').']');
}