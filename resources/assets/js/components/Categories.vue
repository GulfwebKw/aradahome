<template>
   <div class="container">
    <div class="tt-product-listing row">
	  <div class="col-6 col-lg-3 col-md-4 col-sm-6 tt-col-item" v-for="category in laravelData.data" :key="category.id">
			 <div class="tt-product thumbprod-center">
			      <div class="tt-image-box">
				  <a :href="getIconUrl(category.id,category.friendly_url)">
				  <span class="tt-img">
				  <img v-if="category.image" :src="getIconPath(category.image)" alt="">
				  <img v-else  src="/uploads/no-image.png" alt="">
				  </span>
				  </a>
				  </div>
				  <p v-if="locale==='en'"><a :href="getIconUrl(category.id,category.friendly_url)">{{category.name_en}}</a></p>
				  <p v-else><a :href="getIconUrl(category.id,category.friendly_url)">{{category.name_ar}}</a></p>
			 </div>
	  </div>		 
	</div>
	<div class="text-center tt_product_showmore">
	<pagination :data="laravelData" @pagination-change-page="getResults"></pagination>
	</div>
</div>	
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
		props: ['locale']
		,
        data() {
            return {
                laravelData: {},
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
      
                this.$http.get('/listCategoriesVueJs?page=' + page)
                    .then(response => {
                        return response.json();
                    }).then(data => {
                        this.laravelData = data;
                    });
            },
			getIconPath(imagePath) {
                return '/uploads/category/thumb/'+imagePath;
            },
			getIconUrl(id,friendly_url){
			    return '/products/'+id+'/'+friendly_url;
			}
        }
    }
</script>
<style>
.tt-img img{max-width:300px;}
</style>