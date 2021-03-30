<?php

function __get($name) {
  if (!empty($_GET[$name])) return htmlspecialchars($_GET[$name]);
  return NULL;
}

function __post($name) {
  if (!empty($_POST[$name])) return htmlspecialchars($_POST[$name]);
  return NULL;
}