<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ChannelCommandRepository extends DocumentRepository {

    /**
     * @param $network
     * @param $channel
     * @return mixed
     */
    public function findAllByChannel($network, $channel) {
        return $this->createQueryBuilder()
            ->sort('name', 'ASC')
            ->field('network')->equals($network)
            ->field('channel')->equals($channel)
            ->getQuery()
            ->execute();
    }
}
