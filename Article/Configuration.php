<?php
namespace Datto\ORM\Article;

class Configuration {

    private $id;
    private $mac;
    private $timestamp;
    private $configuration;
    private $hash;

    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function setMac($mac) {
        $this->mac = $mac;

        return $this;
    }

    public function getMac() {
        return $this->mac;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function setTimestamps(...$timestamps) {
        $this->timestamp = $timestamps;

        return $this;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function setConfiguration($configuration) {
        $this->configuration = $configuration;

        return $this;
    }

    public function getConfiguration() {
        return $this->configuration;
    }

    public function setHash($hash) {
        $this->hash = $hash;

        return $this;
    }

    public function getHash() {
        return $this->hash;
    }

    public function onSave() {
        $this->timestamp = time();
        $this->hash = hash('sha256', $this->timestamp . $this->configuration);
    }
}
