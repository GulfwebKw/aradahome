<template>
   <div class="container">
      <div v-for="attribute in laravelData" :key="attribute.id">
	  
	  <div v-for="colors in attribute.attr_colors" :key="colors.id">
		   <p v-if="colors.id" class="colordiv"><span class="attitle">{{colors.title_en}}</span> <button type="button" class="btn btn-success btn-sm btn-icon btn-icon-md pull-right"><i class="flaticon2-add-1"></i></button></p>
	  </div>
	  
	  <div v-for="sizes in attribute.attr_sizes" :key="sizes.id">
		   <p v-if="sizes.id" class="colordiv"><span class="attitle">{{sizes.title_en}}</span> <button type="button" class="btn btn-success btn-sm btn-icon btn-icon-md pull-right"><i class="flaticon2-add-1"></i></button></p>
	  </div>
	  
	  <div v-for="attr_sc in attribute.attr_sizescolors" :key="attr_sc.id">
		   <p v-if="attr_sc.id" class="colordiv"><span class="attitle">{{attr_sc.size_name}} - {{attr_sc.color_name}}</span> <button type="button" class="btn btn-success btn-sm btn-icon btn-icon-md pull-right"><i class="flaticon2-add-1"></i></button></p>
	  </div>
	  <div v-for="attr_other_option in attribute.attr_other" :key="attr_other_option.id">
	   <p v-if="attr_other_option.id" class="colordiv"><span class="attitle">{{attr_other_option.option_value_name_en}}</span> <button type="button" class="btn btn-success btn-sm btn-icon btn-icon-md pull-right"><i class="flaticon2-add-1"></i></button></p>
	  </div>
	  
	  
	  	   
	  </div>
   </div>	
</template>

<script>
    export default {
        mounted() {
            console.log('attribute component mounted.')
        },
		props: ['productid','oid']
		,
        data() {
            return {
                laravelData: {}
            }
        },
        created() {
            this.getResults();
        },
        methods: {
            getResults() {
				
                this.$http.get('/PosProductsVueJs_GetAttribute?productid='+this.productid+'&oid='+this.oid)
                    .then(response => {
                        return response.json();
                    }).then(data => {
                        this.laravelData = data;
						//console.log(data);
                    }); 
            }
			
        }
    }
</script>
<style lang="scss" scoped>
.colordiv{
border: 1px solid #3498db;
height:40px;
padding:5px;
}
.attitle{
padding-top:10px;
}
</style>