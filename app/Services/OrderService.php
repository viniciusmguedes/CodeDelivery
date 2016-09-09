<?php
/**
 * Created by PhpStorm.
 * User: vinicius
 * Date: 01/09/2016
 * Time: 02:56
 */

namespace CodeDelivery\Services;


use CodeDelivery\Models\Order;
use CodeDelivery\Repositories\CupomRepository;
use CodeDelivery\Repositories\OrderRepository;
use CodeDelivery\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var CupomRepository
     */
    private $cupomRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        OrderRepository $orderRepository,
        CupomRepository $cupomRepository,
        ProductRepository $productRepository)
    {

        $this->orderRepository = $orderRepository;
        $this->cupomRepository = $cupomRepository;
        $this->productRepository = $productRepository;
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try{
            $data ['status']=0;
            if(isset($data['cupom_code'])){
                $cupom = $this->cupomRepository->findByField('code', $data['cupom_code'])->firt();
                $data['cupom_id'] = $cupom->id;
                $cupom->used = 1;
                $cupom->save();
                unset($data['cupom_code']);
            }
            $items = $data['items'];
            unset($data['items']);

            $order = $this->orderRepository->create($data);
            $total =0;

            foreach ($items as $item){
                $item['price'] = $this->productRepository->find($item['product_id'])->price;
                $order->items()->create($item);
                $total += $item['price'] * $item['qtd'];
            }
            $order->total =$total;
            if(isset($cupom)){
                $order->total = $total - $cupom->value;
            }
            $order->save();
            \DB::commit();
            return $order;
        } catch(\Excpetion $e){
            \DB::rollback();
            throw $e;
        }
    }
    public function updateStatus($id, $idDeliveryman, $status)
    {
        $order = $this->orderRepository->getByIdAndDeliveryman($id, $idDeliveryman);
        if($order instanceof Order)
        {
            $order->status = $status;
            $order->save();
            return $order;
        }
        return false;
    }
}