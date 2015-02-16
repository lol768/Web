<?php

namespace Korobi\WebBundle\Util;

/**
 * Class to handle getting info about git branches, commits etc.
 * @package Korobi\WebBundle\Util
 */
class GitInfo {

    protected $filesystem;
    protected $branch;
    protected $hash;

    /**
     * Initialize the class.
     */
    public function __construct() {
        $this->updateData();
    }

    /**
     * @return string The name of the current git branch.
     */
    public function getBranch() {
        return $this->branch;
    }

    /**
     * @return string The current hash.
     */
    public function getHash() {
        return $this->hash;
    }

    /**
     * @param int $length Length of short hash
     * @return string The shorter hash
     */
    public function getShortHash($length = 7) {
        return substr($this->hash, 0, $length);
    }

    /**
     * Force update data.
     */
    public function updateData() {
        $root = __DIR__ . '/../../../../'; // bit of a hack
        $ref = (new \SplFileObject($root . '.git/HEAD'))->getCurrentLine();
        $ref = trim(explode(' ', $ref)[1]);

        $this->branch = trim(array_reverse(explode('/', $ref))[0]);
        $this->hash = (new \SplFileObject($root . '.git/' . $ref))->getCurrentLine();
    }
}
