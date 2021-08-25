<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewayDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateway_definitions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('image_path')->nullable();
            $table->string('code');
            $table->boolean("active")->default(false);
            $table->string('category');
            $table->timestamps();
        });

        DB::statement("INSERT INTO `payment_gateway_definitions` VALUES (1,'Visa / Mastercard','payment_images/5YaL6jygKpp6BVX1BWtCdQgZ7bDir7KEz5ldTyy1.png','BP-OCBC1',1,'ocbc','2020-06-18 04:44:22','2020-06-18 05:42:49'),(2,'UnionPay','payment_images/dAOuJfMrKqCY1afdSq81ry4SYQ1lqZrJtoYJkYaw.png','BP-2C2PU',1,'2c2p','2020-06-18 04:44:22','2020-06-18 05:42:49'),(3,'PayPal',NULL,'BP-PPL01',1,'paypal','2020-06-18 04:44:22','2020-06-18 05:42:49'),(4,'Boost','payment_images/bZ2l8LRaQB7fKTpFBeEZDlzIpUsnsGk7PrA05sjt.png','BP-BST01',1,'boost','2020-06-18 04:44:22','2020-06-18 05:42:49'),(5,'Senangpay','payment_images/9PMaGFskTKn6geaQCGQmhqsR9C0RWTm5mWxa812F.png','BP-SGP01',1,'senangpay','2020-06-18 04:44:22','2020-06-18 05:42:49'),(6,'Maybank2u','payment_images/PdAYNIknypdxcrsplhkYhy3GFWfuat5hFvvfw6of.png','MB2U0227',0,'fpx','2020-06-18 04:44:22','2020-06-18 05:42:49'),(7,'Mastercard (2c2p)','payment_images/L32nM4u0Wr7XKYTh5ziXIEfGBZUtAuJTVL4iXr4D.png','BP-2C2PC',1,'2c2p','2020-06-18 04:44:22','2020-06-18 05:42:49'),(8,'e-Pay','payment_images/VIAp2SK9Q6QACqDd5smkDtvGlUy79Gc3PksQKwcb.png','BP-2C2P1',1,'2c2p','2020-06-18 04:44:22','2020-06-18 05:42:49'),(9,'CIMB Clicks','payment_images/Vc7BhtVLMgqJNYpBaFEYRs6aMY7Ry1uFrxoJUzhf.png','BCBB0235',1,'fpx','2020-06-18 04:44:22','2020-06-18 05:42:49'),(10,'RHB Bank','payment_images/c9j7mqVmAuvffipAi8662QPgFH7fdkNtcKmzYlo3.png','RHB0218',1,'fpx','2020-06-18 04:44:22','2020-06-18 05:42:49'),(11,'Public Bank','payment_images/tW1GYaZTGS0Gz6CA4nQ6Em6FzGM7WFpJlFb1HFbF.png','PBB0233',1,'fpx','2020-06-18 04:44:22','2020-06-18 05:42:49'),(12,'Hong Leong bank','payment_images/pvJnEfYZxTKtKPZLCNWW8Ly93JfNLO0EcnyKLbxr.png','HLB0224',1,'fpx','2020-06-18 04:44:22','2020-06-18 05:42:49'),(13,'Affin bank','payment_images/7dzpzMAUdklJzO4yhVGvUsuELij70d6lXvEp0ZpX.png','ABB0233',1,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(14,'Aliance Bank','payment_images/gen4m79EEFyuTWaFLRZVu72NEMp77vsvL1UjcS4B.png','ABMB0212',0,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(15,'AmBank','payment_images/R4iCJk6tv3Z1m8Y9KekPSmOTV2JLeABcr1MsXVWq.png','AMBB0209',1,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(16,'Bank Islam','payment_images/DGrOqroPPJwywHXpEBGlJSSnAufqOy8HPbWjUTS2.png','BIMB0340',1,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(17,'Bank Muamalat','payment_images/aVAAb9I5GUlcfZVww5FFGc5h4BF6Jv87DtO6Ri25.png','BMMB0341',1,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(18,'Bank Rakyat','payment_images/fdAhhTnOhb78W8cj8zxHc1mramIxJRiuECew7is7.png','BKRM0602',1,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(19,'Bank Simpanan National','payment_images/ZtNPcXgJeYImVJ4jr1ssOn6gFYvu6K2kZJFQzVHW.png','BSN0601',1,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(20,'HSBC Bank','payment_images/jcwFURhRtlUAz1CnzSpO0iLmYyK0e72JuPuzNq0L.png','HSBC0223',1,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(21,'Kuwait Finance House','payment_images/uNnzj105qwWBql6vWUa2NgNdhWbjTg42hT2AIEdP.png','KFH0346',1,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(22,'OCBC Bank','payment_images/nggLY4qxoFQZGNkRxyCp7CRibmTsVtOvfmrYz2cM.png','OCBC0229',1,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(23,'Standard Chartered','payment_images/C154CwB6cCssxWztGJ1Qt0bOt1PBr5Q16MpGMJxr.png','SCB0216',0,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(24,'UOB Bank','payment_images/YPrifLTWwLAPjUBaEepjUX6YujXlZLClNZarKVGY.png','UOB0226',0,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(25,'Maybank2E','payment_images/NwDRG0XoloxQ8GwJ5cgo8sHv5SYhpuxsQcFKtHSc.png','MBB0227',1,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(26,'Maybank2E','payment_images/fFvnrsQSRK9jqsilYoYkyFCB5l29mnda3LrtMSbG.png','MBB0228',0,'fpx','2020-06-18 04:44:23','2020-06-18 05:42:49'),(27,NULL,NULL,'CIT0219',0,'fpx','2020-06-18 05:42:49','2020-06-18 05:42:49'),(28,NULL,NULL,'AGRO01',0,'fpx','2020-06-18 05:42:49','2020-06-18 05:42:49'),(29,NULL,NULL,'ABB0234',1,'fpx','2020-06-18 05:42:49','2020-06-18 05:42:49'),(30,NULL,NULL,'BP-FKR01',1,'billplz','2020-06-18 05:42:49','2020-06-18 05:42:49'),(31,NULL,NULL,'TEST0002',0,'fpx','2020-06-18 05:42:49','2020-06-18 05:42:49'),(32,NULL,NULL,'TEST0003',0,'fpx','2020-06-18 05:42:49','2020-06-18 05:42:49'),(33,NULL,NULL,'TEST0004',0,'fpx','2020-06-18 05:42:49','2020-06-18 05:42:49'),(34,NULL,NULL,'TEST0021',1,'fpx','2020-06-18 05:42:49','2020-06-18 05:42:49'),(35,NULL,NULL,'TEST0022',1,'fpx','2020-06-18 05:42:49','2020-06-18 05:42:49'),(36,'UOB Bank','payment_images/OrJg6hBbw94Jh9YYIg48GIhMytvX6v9OjRpDJRf7.png','UOB0229',1,'fpx','2020-06-18 05:42:49','2020-06-18 05:45:15'),(37,'Billplz','payment_images/q9YvciMnwRhFTKBbxLBDCNIv56VxNvxLUYpg1DMV.png','TEST0023',1,'fpx','2020-06-18 05:42:49','2020-06-18 13:44:32'),(38,NULL,NULL,'LOAD001',1,'fpx','2020-06-18 05:42:49','2020-06-18 05:42:49'),(39,NULL,NULL,'TEST0001',0,'fpx','2020-06-18 05:42:49','2020-06-18 05:42:49');
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_gateway_definitions');
    }
}
