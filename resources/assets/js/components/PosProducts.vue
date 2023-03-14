<template>
<div class="kt-portlet kt-portlet--last">

										<div class="kt-portlet__body">
											<div class="kt-searchbar">
												<div class="input-group">
													<div class="input-group-prepend"><span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
																<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																	<rect x="0" y="0" width="24" height="24"></rect>
																	<path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
																	<path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero"></path>
																</g>
															</svg></span></div>
													<input type="text" @keyup="searchUnit" v-model="search" class="form-control" placeholder="Search" aria-describedby="basic-addon1">
												</div>
											</div>
											<div class="kt-widget kt-widget--users kt-mt-20 loadercontainer">
											<div v-if="loading" class="loader"></div>
												<div class="kt-scroll kt-scroll--pull ps ps--active-y" style="height: 267px; overflow: hidden;">
												
  <div class="kt-widget__items" v-for="product in laravelData.data" :key="product.id">
  
           <div class="kt-widget__item" >
		   <span class="kt-media kt-media--quare">
		   <img v-if="product.image" :src="getIconPath(product.image)" alt="">
		   <img v-else  src="/uploads/no-image.png" alt="">
		   </span>
	       <div class="kt-widget__info">
		   <div class="kt-widget__section">
		   <a href="#" class="kt-widget__username">{{product.title_en}}</a>
		   <span class="kt-badge kt-badge--warning">&nbsp;<b v-if="product.item_code">{{product.item_code}}</b>&nbsp;</span>
		   </div>
		   <span class="kt-widget__desc" v-if="product.retail_price">{{product.retail_price}} KD <small v-if="product.old_price"><s>{{product.old_price}} KD</s></small></span>
		   </div>
		   <div class="kt-widget__action">
		    <button v-if="product.is_attribute==0" type="button" class="btn btn-success btn-sm btn-icon btn-icon-md"><i class="flaticon2-add-1"></i></button>
		   </div>
	       </div>
	       
		   <div class="widediv" >
		   <attribute-component :productid="product.id" :oid="myprop"></attribute-component>
		   </div>
	  
	</div>
	
	
 </div>
 </div>
      <div class="kt-portlet__foot">
	    
		<product-pagination :data="laravelData" @pagination-change-page="getResults"></product-pagination>
	  </div>
	</div>
	
    <b-modal ref="modal_product" hide-footer  size="lg" :no-close-on-backdrop="true" title="Options">
      <div class="d-block text-center">
        <div v-if="response.message" >{{response.message}}</div>
      </div>
    </b-modal>
 
 
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
			    myprop:this.oid,
                laravelData: {},
				loading: false,
				search: '',
				response: {
					message: '',
					status: '',
					state: ''
				},
				resdata:{}
            }
        },
        created() {
            this.getResults();
        },
        methods: {
            getResults(page) {
                if (typeof page === 'undefined') {
                    page = 1;
                }
				
				this.loading = true;
      
                this.$http.get('/PosProductsVueJs?page=' + page)
                    .then(response => {
                        return response.json();
                    }).then(data => {
                        this.laravelData = data;
						this.loading = false;
                    });
            },
			searchUnit:_.debounce(function(){
			  this.$http.get('/PosProductsVueJs?q='+this.search)
					.then(response => {
                        return response.json();
                    }).then(data => {
                        this.laravelData = data;
						this.loading = false;
                    });
			}),
			getIconPath(imagePath) {
                return '/uploads/product/thumb/'+imagePath;
            },
			getIconUrl(id,friendly_url){
			    return '/products/'+id+'/'+friendly_url;
			},
			showModal(id) {
				let vm         = this;
				let resdata    = {};
				resdata['id']  = id;
				resdata['oid'] = this.oid;
				$.ajax({
					url: '/PosProductsVueJs_GetAttribute',
					data: resdata,
					type: "GET",
					dataType: 'json',
					success: function(e) {
						vm.response = e;
					}
				});
			   
			  this.$refs['modal_product'].show();
			},
			hideModal() {
			  console.log('Modal clicked')
			  this.$refs['modal_product'].hide()
			}
        }
    }
</script>
<style lang="scss" scoped>
.widediv{
border: 1px solid #ffffff;
width:400px;
clear:both;
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