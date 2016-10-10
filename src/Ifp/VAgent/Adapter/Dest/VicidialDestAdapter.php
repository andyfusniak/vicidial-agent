<?php
namespace Ifp\VAgent\Adapter\Dest;

use Ifp\VAgent\Resource\Item;
use Ifp\Vicidial\VicidialApiGateway;
use Monolog\Logger;

class VicidialDestAdapter implements DestAdapterInterface
{
    /**
     * @var VicidialApiGateway
     */
    protected $vicidialApiGateway;

    /**
     * @var Logger
     */
    protected $log;

    /**
     * @param VicidialApiGateway $vicidialApiGateway instance
     */
    public function __construct(VicidialApiGateway $vicidialApiGateway)
    {
        $this->vicidialApiGateway = $vicidialApiGateway;
    }

    /**
     * Push an item to the VICIdial API using the VicidialApiGateway PHP library
     * @param Item $item to push
     */
    public function pushItem(Item $item)
    {
        $this->vicidialApiGateway->softReset();
        $this->vicidialApiGateway->setAction(
            VicidialApiGateway::ACTION_ADD_LEAD
        )->addParam(
            VicidialApiGateway::REQUIRED_PARAM_PHONE_NUMBER, $item->getPhoneNumber()
        )->addParam(
            'first_name', $item->getFirstName()
        )->addParam(
            'last_name', $item->getLastName()
        )->addParam(
            'id', $item->getId()
        );

        foreach ($item->getCustomParams() as $name => $value) {
            if (null === $value) {
                $value = '';
            }
            $this->vicidialApiGateway->addParam($name, $value);
        }

        if ($this->log) {
            $this->log->debug('Calling VicidialApiGateway');
            $this->log->debug('API URI ', [$this->vicidialApiGateway->getHttpQueryUri()]);
        }
        $result = $this->vicidialApiGateway->apiCall();
        
        if ($this->log) {
            $this->log->debug('Response: ', [$this->vicidialApiGateway->getApiResponseMessage()]);
        }
        return $result;
    }
    
    /**
     * Currently returns an empty Item object
     *
     * @todo not yet implemented
     */
    public function getItem(string $id)
    {
        return new Item();
    }

    /**
     * Inject the Logger dependency (optional)
     *
     * @param Logger
     * @return VicidialDestAdapter
     */
    public function setLogger($logger)
    {
        $this->log = $logger;
        return $this;
    }
}