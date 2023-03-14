<template>
 <div class="kt-chat">
										<div class="kt-portlet kt-portlet--head-lg kt-portlet--last">
											<div  style="min-height:30px !important; border:0;">
													
														<table class="table table-striped-  table-hover table-checkable" >
														<thead>
															<tr>
                                                                <th class="imagetd">IMAGE</th>
																<th style="text-align:left;" >DETAILS</th>
																<th style="text-align:center;" class="unitprice">UNIT PRICE</th>
																<th style="text-align:center;" class="quantity">QUANTITY</th>
																<th style="text-align:center;" class="subtotal">SUB TOTAL</th>
															</tr>
														</thead>
                                                        </table>
													
											</div>
											
                         <div class="kt-portlet__body loadercontainer" style="padding:10px !important;">
						 <div v-if="loading" class="loader"></div>
                         <div class="kt-scroll kt-scroll--pull ps ps--active-y" data-mobile-height="300" style="height: 89px; overflow: hidden;">
                         <div class="kt-chat__messages">
                         <table class="table" >
                         <tr v-for="(orders,nindex) in laravelData" :key="nindex">
                         <td class="imagetd">
                         <span class="kt-media kt-media--quare">
                         <img v-if="orders.image" :src="getIconPath(orders.image)" alt="">
                         <img v-else  src="/uploads/no-image.png" alt="">
                         </span>
                         </td>
                         <td style="text-align:left;"><b v-if="orders.title_en">{{orders.title_en}}</b></td>
                         <td class="unitprice">{{orders.unit_price}}</td>
                         <td align="center" class="quantity">
						 <a href="javascript:;" class="minus" @click="deleteto(orders.id)">-</a>
                         <input type="text" class="num" value="1" v-on:change="event => updateQuantity(event)" :id="orders.id"  v-model.number="orders.quantity"  required="required" >
                         <a href="javascript:;" class="plus" @click="add(orders.id)">+</a>
						 </td>
                         <td align="center" class="subtotal"><div align="center">{{(orders.quantity)*(orders.unit_price)}}</div></td>
                         </tr>
                         </table>
                         </div>	
                         </div>
                         </div>
 
 <div class="kt-portlet__foot">
 <div class="kt-invoice__total pull-right invoicetotal">
 <div class="invoice-item totalamount">
 <span class="kt-invoice__title">Total</span>
 <span class="kt-invoice__price pull-right">{{total}}</span>
 </div>
 <div class="invoice-item discount">
 <span class="kt-invoice__title">Discount</span>
 <span class="kt-invoice__price pull-right"><input type="text" name="discountInput" v-model="couponDiscount"  class="discountInputBox"></span>
 </div>
 <div class="invoice-item deliveryfees">
 <span class="kt-invoice__title">Delivery Fees</span>
 <span class="kt-invoice__price pull-right">{{deliveryCharges}}</span>
 </div>
 <div class="invoice-item grandtotal">
 <span class="kt-invoice__title">Grand Total</span>
 <span class="kt-invoice__price pull-right">{{grandTotal}}</span>
 </div>
 </div> 
 </div>	
                            
										</div>
									</div>
					
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
		props: ['locale','oid']
		,
        data() {
            return {
			    loading: false,
				grandTotal:0,
				couponDiscount:0,
				deliveryCharges:0,
			    total: 0,
                laravelData: {}
            }
        },
        created() {
            this.getResults();
        },
        methods: {
            getResults() {
                this.$http.get('/PosCartVueJs?oid=' + this.oid)
                    .then(response => {
                        return response.json();
                    }).then(data => {
                        this.laravelData = data.ordersLists;
						this.loading = false;
						
						this.grandTotal      = data.ordersDetails.grand_total;
						this.couponDiscount  = data.ordersDetails.coupon_fee;
						this.deliveryCharges = data.ordersDetails.delivery_charges;
						this.total           = data.ordersDetails.total_amount;
						
                    });
            },
			getIconPath(imagePath) {
                return '/uploads/product/thumb/'+imagePath;
            },
			updateQuantity: function(event){
             let qty = event.target.value;
			 let id  = event.currentTarget.id;
			 
			 this.$http.get('/PosCartTotalVueJs?id=' + this.id + '&qty=' + qty)
                    .then(response => {
                        return response.json();
                    }).then(data => {
						this.grandTotal      = data.ordersDetails.grand_total;
						this.couponDiscount  = data.ordersDetails.coupon_fee;
						this.deliveryCharges = data.ordersDetails.delivery_charges;
						this.total           = data.ordersDetails.total_amount;
                    });
            }
			 
			 
			 
            },
		computed: {
			 
		}
    }
</script>
<style lang="scss" scoped>
.minus{
width:30px;
padding:3px;
border:1px solid #ccc;
float:left;
}
.plus{
width:30px;
padding:3px;
border:1px solid #ccc;
float:right;
}
.num{
width:30px;
padding:3px;
border:1px solid #ccc;
float:left;
}
.invoicetotal{
		width:300px; 
		border-bottom:1px #CCCCCC solid;
		}
		.totalamount{
		 font-size:14px;
		 font-weight:bold;
		 height:40px;
		}
		.deliveryfees{
		font-size:14px;
		height:40px;
		}
		.discount{
		font-size:14px;
		color:#FF0000;
		height:40px;
		}
		.grandtotal{
		 font-size:14px;
		 font-weight:bold;
		 height:40px;
		}
		.imagetd{
		width:60px;
		}
		.unitprice{
		 width:100px;
		 text-align:center;
		}
		.quantity{
		 width:110px;
		 text-align:center;
		}
		.subtotal{
		 width:100px;
		 text-align:center;
		}
.discountInputBox{
width:100px;
}
.kt-badge--warning{
min-width:50px;
border-radius:0;
}
.loadercontainer .loader {
  margin-left:50px;
  position:absolute;
  border: 16px solid #f3f3f3;
  border-top: 16px solid #3498db;
  border-radius: 50%;
  width: 100px;
  height: 100px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>