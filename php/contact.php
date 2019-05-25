<?php

$response = [
  'error' => null,
  'success' => false,
];

if (!isset($_POST['name']) || !isset($_POST['email']) ||
    !isset($_POST['subject']) || !isset($_POST['message'])) {
  $response['error'] = 'Invalid request, data missing.';
}
elseif (preg_match('/[\r\n]/', $_POST['name'])) {
  $response['error'] = 'Invalid name, cannot contain new line.';
}
elseif (preg_match('/[\r\n]/', $_POST['email'])) {
  $response['error'] = 'Invalid email, cannot contain new line.';
}
elseif (preg_match('/[\r\n]/', $_POST['subject'])) {
  $response['error'] = 'Invalid subject, cannot contain new line.';
}
else {
  $data = [
    'userdata' => $_POST['name'].' <'.$_POST['email'].'>',
    'from' => $_POST['name'].' <contact@nancolin.nl>',
    'sender' => 'Bold Design Contact <contact@nancolin.nl>',
    'subject' => $_POST['subject'],
    'message' => quoted_printable_encode($_POST['message']),
  ];

  $headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=utf-8',
    'Content-Transfer-Encoding: quoted-printable',
    'From: '.$data['from'],
    'Sender: '.$data['sender'],
    'Reply-To: '.$data['userdata'],
  ];

  /* Windows servers only! */
  // $data['message'] = str_replace("\n.", "\n..", $data['message']);

  $response['success'] = mail('nancolin92@gmail.com', $data['subject'], $data['message'], implode("\r\n", $headers));

  if (!$response['success']) {
    $response['error'] = error_get_last()['message'];
  }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
die();
