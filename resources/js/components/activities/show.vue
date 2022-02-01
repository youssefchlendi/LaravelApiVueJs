<template>
<div>
  <div class="card card-body  my-2" v-for="activity in activities"  :key="activity.id">
          <div>
              <h3 class="text-white bg-danger text-center" :class="show(activity)?'d-none':'d-block'">Passed The Date</h3>
              <h3 class="d-inline">{{activity.activity_name}}</h3>
             <span v-for="tag in activity.tags" :key="tag.id" class="badge bg-info badge-info p-2 m-2" >{{tag.tag_name}}</span>

              <b-icon class="float-end" v-b-tooltip.hover="{ variant: 'success',title:'Activity completed',placement:'topright'}" v-if="activity.items.every(e => e.status)||!activity.items.length" icon="check-square" scale="2" variant="success"></b-icon>
              <b-icon class="float-end" v-b-tooltip.hover="{ variant: 'danger',title:'Activity not completed',placement:'topright'}" v-else icon="x-square" scale="2" variant="danger"></b-icon>
          </div>
          <ul class="list-group list-group-flush">
              <li class="list-group-item" :class="isPassed(item.dead_line)?'bg-warning shadow-lg':'bg-light'" v-for="item in activity.items" :key="item.id">
                  <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" :checked="item.status" @change="updateStatus(item)" v-model="item.status" role="switch" id="flexSwitchCheckDefault">
                      <label class="form-check-label" for="flexSwitchCheckDefault">{{item.item_name}}</label>
                      <button class="btn btn-danger float-end" @click="deleteTask(item.id,activity.id)">Delete</button>
                      <button class="btn btn-warning float-end mx-2" @click="editTask(item)">Edit</button>
                  </div>
              </li>
          </ul>
          <hr>
          <b-button v-b-modal.modal-1 @click="update(activity.id)">Add task</b-button>
          <button class="btn btn-warning mb-2" @click="editActivity(activity)" data-bs-toggle="modal" data-bs-target="#exampleModal">Edit</button>
          <button class="btn btn-danger" @click="deleteActivity(activity.id)">Delete</button>
      </div>
      <div class="card card-body my-2" v-if="activities.length==0">
          <h3>There's no activities</h3>
      </div>
      <nav aria-label="..." class="row ">
          <ul class="pagination w-auto mx-auto">
              <li :class="[{disabled:!pagination.prev_page_url}]" class="page-item ">
                  <a  @click="fetchActivities(pagination.prev_page_url)" class="btn page-link">Previous</a>
              </li>
              <li class="page-item"><a class="page-link text-dark" href="#">{{pagination.current_page+"/"+pagination.last_page}}</a></li>
              <li :class="[{disabled:!pagination.next_page_url}]" class="page-item">
                  <a  @click="fetchActivities(pagination.next_page_url)" class="btn page-link" >Next</a>
              </li>
          </ul>
      </nav>
</div>
</template>

<script>
export default {

    props:{
        activities : Array,
        pagination : Object,
    },
    emits:['updateStatus','deleteTask','editTask','editActivity','deleteActivity','update'],
    methods : {
        isPassed(date) {
            let d = new Date();
            let d1 = new Date(date);
            return d1 < d;
        },
        show(activity){
          return !activity.items.some(e => this.isPassed(e.dead_line));
        },
        updateStatus(item){
            this.$emit('updateStatus', item);
        },
        deleteTask(itemId,activityId){
            this.$emit('deleteTask', itemId, activityId);
        },
        editTask(item){
            this.$emit('editTask', item);
        },
        editActivity(activity){
            this.$emit('editActivity', activity);
        },
        deleteActivity(activityId){
            this.$emit('deleteActivity', activityId);
        },
        update(activityId){
            this.$emit('update', activityId);
        }
    }

}
</script>

<style>

</style>
