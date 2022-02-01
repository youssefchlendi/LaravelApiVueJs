<template>
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">New activity</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form class="mb-3" @submit.prevent="addActivity">
                  <div class="modal-body">
                          <div class="form-group mb-2">
                              <label for="">Activity Name</label>
                              <input type="text" class="form-control" placeholder="Activity Name" v-model="activity.activity_name">
                              <label for="">Activity priority</label>
                              <select class="form-select" v-model="activity.priority" aria-label="Priority">
                                  <option v-for="priority in priorities" :selected="activity.priority==priority" :key="priority.id" :value="priority">{{priority}}</option>
                              </select>
                              <label for="">Tags</label>
                              <div class="shadow-lg bg-white input-group mb-2 d-block">
                                  <div class="input-group mb-2">
                                  <input type="text" class="form-control" placeholder="Add new tag" v-model="label" >
                                    <div class="input-group-append">
                                  <button type="button" @click="addLabel" class="btn btn-success">Add</button>
                                      </div>
                              </div>
                               <ul>
                                            <li v-for="tag in tags" class="container" :key="tag.id">
                                                <input class="form-check-input checks" :checked="containsObject(tag)" type="checkbox" @change="pushTo(tag.id)"  role="switch" id="flexSwitchCheckDefault">
                                                <div class="row shadow-sm">
                                                    <div class="col-6">
                                                {{ tag.tag_name }}
                                                    </div>
                                                    <div class="col-6">
                                                <button type="button" class="float-end btn" @click="deleteLabel(tag.id)" ><b-icon icon="trash" scale="1" variant="danger"></b-icon></button>
                                                    </div>
                                                </div>
                                            </li>
                                      </ul>
                                      </div>
                          </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" @click='resetModal1' data-bs-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary " @click="addActivity" data-bs-dismiss="modal" >Add</button>
                  </div>
                  </form>
              </div>
          </div>
      </div>
</template>

<script>
export default {
    emits:['addLabel'],
    props:{
        tags:Array,
        oldActivity : Object,
        edit : Boolean,
    },
    data(){
        return{
            activity:this.oldActivity,
            activity_id:"",
            priorities:['low','medium','high','DANGER'],
            label:'',
        }
    },
    methods:{
        pushTo(id){
            this.activity.tags.indexOf(id)==-1?this.activity.tags.push(id):this.activity.tags.splice(this.activity.tags.indexOf(id)!=-1,1);
        },
        addLabel(){
            this.$emit('addLabel',this.label);
        },
        resetModal1() {
            this.edit=false;
            this.activity.activity_name=this.activity_id='';
        },
        addActivity(){
            this.$emit('addActivity',this.activity);
            this.activity.activity_name = '';
        },
        containsObject(obj) {
            var i;
            let list=this.activity.tags;
            for (i = 0; i < list.length; i++) {
                if (list[i].id == obj.id&&this.edit==true) {
                    return true;
                }
            }
            return false;
        },
         activated(){
             document.getElementsByClassName('checks').forEach((e)=>{e.checked=false;});
         }
    }

}
</script>

<style>

</style>
