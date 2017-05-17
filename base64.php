<?php
namespace tomlib\base64;

function url_encode($input) {
 return strtr(base64_encode($input), '+/=', '-_,');
}
