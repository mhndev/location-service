<?php

//http://stackoverflow.com/questions/26206685/how-can-i-describe-complex-json-model-in-swagger
//http://editor.swagger.io
//https://gist.github.com/nostah/d610459d50564c729c56

/**
 *
 * @SWG\Definition(
 *   definition="Extra",
 *   required={},
 *   @SWG\Property(property="from", title="from", type="Point", @SWG\Schema(ref="Point")),
 * 	 @SWG\Property(property="to", title="to", type="Point", @SWG\Schema(ref="Point")),
 * ),
 *
 * @SWG\Definition(
 *   definition="Point",
 *   required={},
 *   @SWG\Property(property="lat", title="lat", type="float", example="37.3243546"),
 * 	 @SWG\Property(property="lon", title="lon", type="float", example="51.2345634"),
 * ),
 *
 *
 * @SWG\Definition(
 *   definition="OrderItem",
 *   required={"price", "itemType", "itemIdentifier", "extra"},
 *   @SWG\Property(property="identifier", title="identifier", type="string", description="UUID", example="24rf2rf3"),
 * 	 @SWG\Property(property="itemType", title="itemType", type="string", example="peik"),
 * 	 @SWG\Property(property="itemIdentifier", title="itemIdentifier", type="string", example="2"),
 * 	 @SWG\Property(property="extra", title="extra",type="Extra", @SWG\Schema(ref="Extra"), example="{}"),
 * ),
 *
 * @SWG\Definition(
 *   definition="Order",
 *   required={"status"},
 *   @SWG\Property(property="identifier", title="identifier", type="string", description="UUID", example="42t34gt34t"),
 * 	 @SWG\Property(property="status", title="status", type="string", example="0"),
 * 	 @SWG\Property(property="data", title="date", type="string", example="23456723"),
 *   @SWG\Property(property="items", title="items", type="array", items = {"$ref" : "OrderItem"}),
 * ),
 *
 *
 *
 *
 *
 */




/**
 * @SWG\Swagger(
 *     basePath="",
 *     host="192.168.21.46:7000",
 *     schemes={"http"},
 *     @SWG\Info(
 *         version="1.0",
 *         title="OrderService",
 *         @SWG\Contact(name="Majid Abdolhosseini", url="http://www.mhndev.com"),
 *     ),
 *     @SWG\Definition(
 *         definition="Error",
 *         required={"code", "message"},
 *         @SWG\Property(
 *             property="code",
 *             type="integer",
 *             format="int32"
 *         ),
 *         @SWG\Property(
 *             property="message",
 *             type="string"
 *         )
 *     )
 * )
 */



/**
 *
 * @SWG\Get(
 *     path="/order/{id}",
 *     description="Returns specified Order",
 *     summary="show an order",
 *     operationId="api.order.show",
 *     produces={"application/json"},
 *     tags={"Order"},
 *     @SWG\Parameter(
 *        in = "path",
 *        name = "id",
 *        description = "order id to show",
 *        required = true,
 *        type = "string"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="order show."
 *     ),
 *     @SWG\Response(
 *         response=401,
 *         description="Unauthorized action.",
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Not found.",
 *     )
 * )
 */






/**
 *
 * @SWG\Get(
 *     path="/order/me",
 *     description="Returns list of my Orders",
 *     summary="show my orders",
 *     operationId="api.order.me",
 *     produces={"application/json"},
 *     tags={"Order"},
 *     @SWG\Response(
 *         response=200,
 *         description="orders list."
 *     ),
 *     @SWG\Response(
 *         response=401,
 *         description="Unauthorized action.",
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Not found.",
 *     )
 * )
 */




/**
 *
 * @SWG\Get(
 *     path="/order",
 *     description="Returns list of all Orders",
 *     summary="list orders",
 *     operationId="api.order.all",
 *     produces={"application/json"},
 *     tags={"Order"},
 *     @SWG\Response(
 *         response=200,
 *         description="orders list."
 *     ),
 *     @SWG\Response(
 *         response=401,
 *         description="Unauthorized action.",
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Not found.",
 *     )
 * )
 */


/**
 *
 * @SWG\Post(
 *     path="/order",
 *     description="Create an Order and return it as response",
 *     summary="create an order",
 *     operationId="api.order.create",
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     tags={"Order"},
 *     @SWG\Parameter(
 *        in = "body",
 *        name = "body",
 *        description = "order",
 *        required = true,
 *        @SWG\Schema(ref="Order"),
 *     ),
 *
 *     @SWG\Response(
 *         response=201,
 *         description="order created.",
 *         @SWG\Schema(ref="Order"),
 *     ),
 *     @SWG\Response(
 *         response=401,
 *         description="Unauthorized action.",
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Not found.",
 *     )
 * )
 */




/**
 *
 * @SWG\Patch(
 *     path="/order/{id}",
 *     description="change status of and order",
 *     summary="change order status",
 *     operationId="api.order.change-status",
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     tags={"Order"},
 *     @SWG\Parameter(
 *        in = "path",
 *        name = "id",
 *        type = "string",
 *        description = "order id",
 *        required = true,
 *     ),
 *
 *     @SWG\Parameter(
 *        in = "body",
 *        name = "body",
 *        type = "string",
 *        description = "change instruction set",
 *        @SWG\Schema(ref=""),
 *        required = true,
 *     ),
 *
 *
 *     @SWG\Response(
 *         response=204,
 *         description="No Content.",
 *     ),
 *
 *     @SWG\Response(
 *         response=401,
 *         description="Unauthorized action.",
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Not found.",
 *     )
 * )
 */





/**
 *
 * @SWG\Delete(
 *     path="/order/{id}",
 *     description="delete an order",
 *     summary="delete an order",
 *     operationId="api.order.delete",
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     tags={"Order"},
 *     @SWG\Parameter(
 *        in = "path",
 *        name = "id",
 *        type = "string",
 *        description = "order id",
 *        required = true,
 *     ),
 *
 *     @SWG\Response(
 *         response=204,
 *         description="No Content.",
 *     ),
 *
 *     @SWG\Response(
 *         response=401,
 *         description="Unauthorized action.",
 *     ),
 * )
 */



/**
 *
 * @SWG\Put(
 *     path="/order/{id}",
 *     description="update an Order and return it as response",
 *     summary="update an order",
 *     operationId="api.order.create",
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     tags={"Order"},
 *     @SWG\Parameter(
 *        in = "body",
 *        name = "body",
 *        description = "order",
 *        required = true,
 *        @SWG\Schema(ref="Order"),
 *     ),
 *     @SWG\Parameter(
 *        in = "path",
 *        name = "id",
 *        type = "string",
 *        description = "order id",
 *        required = true,
 *     ),
 *
 *     @SWG\Response(
 *         response=201,
 *         description="order created.",
 *         @SWG\Schema(ref="Order"),
 *     ),
 *     @SWG\Response(
 *         response=401,
 *         description="Unauthorized action.",
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Not found.",
 *     )
 * )
 */
