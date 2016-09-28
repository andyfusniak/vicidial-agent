<?php
namespace Ifp\VAgent\Adapter\Dest;

use Ifp\VAgent\Resource\Item;
use Ifp\Vicidial\VicidialApiGateway;

class VicidialDestAdapter implements DestAdapterInterface
{
    /**
     * @var VicidialApiGateway
     */
    protected $vicidialApiGateway;

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
        );

        foreach ($item->getCustomParams() as $name => $value) {
            $this->vicidialApiGateway->addParam($name, $value);
        }

        var_dump('Pushing to VicidialApiGateway');
        var_dump($this->vicidialApiGateway->getHttpQueryUri());
        $result = $this->vicidialApiGateway->apiCall();
        var_dump($this->vicidialApiGateway->getApiResponseMessage());

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
}