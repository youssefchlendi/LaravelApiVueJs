<template>
  <div>
      <!-- TODO:ADD filter by tags -->
      <h2>
          Activities
      </h2>
      <form action="javascript:" class="search-bar">
          <input
              id="search"
              type="search"
              name="search"
              pattern=".*\S.*"
              @keyup="fetchActivities(pagination.current_page_url)"
              required
              v-model="search"
          />
          <button class="search-btn" @click="fetchActivities(pagination.current_page_url)" type="submit">
              <span>Search</span>
          </button>
      </form>
      <!-- Button trigger modal -->
      <b-container class="bv-example-row">
          <b-row class="text-center">
          <b-col cols="8">
              <button type="button" class="btn btn-primary mx-1 float-start"  @click="resetModal1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                  New Activity
              </button>
              <button type="button" class="btn mx-1 float-start" :disabled="activities.length==0" :class="!allValid?'btn-success':'btn-warning'" @click="validateTask">
                  {{ !this.allValid?'Validate all':'Unvalidate all' }}
              </button>
              <button type="button" class="btn btn-danger mx-1 float-start" :disabled="activities.length==0" @click="deleteAll">
                  Delete all
              </button>
          </b-col>
          <b-col>
                <b-dropdown id="dropdown-1" text="Filter by tags" class="m-md-2 ml-auto float-end">
                    <b-dropdown-item :active="filter==''" @click="filterBy('')">All</b-dropdown-item>
                  <b-dropdown-item v-for='tag in tags' :key="tag.id" :active="filter==tag.id" @click="filterBy(tag.id)">{{ tag.tag_name }}</b-dropdown-item>
              </b-dropdown>

              <b-dropdown id="dropdown-1" text="Sort by" class="m-md-2 ml-auto float-end">
                  <b-dropdown-item :active="sort=='priority'" @click="fetchActivities('/api/v1/activities','priority')">Priority</b-dropdown-item>
                  <b-dropdown-item :active="sort=='created_at'" @click="fetchActivities('/api/v1/activities','created_at')">Created</b-dropdown-item>
              </b-dropdown>
          </b-col>
          </b-row>
      </b-container>
      <!-- Modal -->
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
                                                <input class="form-check-input checks" :checked="containsObject(tag)&&edit" type="checkbox" @change="pushTo(tag.id)"  role="switch" id="flexSwitchCheckDefault">
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
                      <button type="submit" class="btn btn-primary " data-bs-dismiss="modal">Add</button>
                  </div>
                  </form>
              </div>
          </div>
      </div>
      <div>
          <b-modal id="-1" title="Add task">
              <form class="mb-3" @submit.prevent="addItem">
                  <div class="form-group mb-2">
                      <input type="text" class="form-control" placeholder="Task Name" v-model="item.item_name" required>
                  </div>
                  <button type="submit" class="btn btn-primary btn-block" >save</button>
              </form>
          </b-modal>
          <b-modal
              id="modal-1"
              ref="modal"
              title="Add task"
              @hidden="resetModal"
              @ok="handleOk"
          >
              <form ref="form" @submit.stop.prevent="handleSubmit">
                  <b-form-group
                      label="Task Name"
                      label-for="name-input"
                      invalid-feedback="Name is required"
                      :state="itemNameState"
                  >
                      <b-form-input
                          id="name-input"
                          v-model="item.item_name"
                          :state="itemNameState"
                          required
                      ></b-form-input>
                      <label for="datepicker">Choose a date</label>
                      <b-form-datepicker id="datepicker" :min="new Date()" v-model="item.dead_line" class="mb-2"></b-form-datepicker>
                  </b-form-group>
              </form>
          </b-modal>
      </div>

      <!--<form class="mb-3" @submit.prevent="addActivity">
          <div class="form-group mb-2">
              <input type="text" class="form-control" placeholder="Activity Name" v-model="activity.activity_name">
          </div>
          <button type="submit" class="btn btn-primary btn-block" >save</button>
      </form>-->
      <div class="card card-body  my-2" v-for="activity in activities"  :key="activity.id">
          <div>
              <h3 class="text-white bg-danger text-center" :class="show(activity)?'d-none':'d-block'">Passed The Date</h3>
              <h3 class="d-inline">{{activity.activity_name}}</h3>
              <b-dropdown id="dropdown-left" v-if="activity.tags.length!=0" text="Tags" variant="secondary" class="m-2">
                <b-dropdown-item v-for="tag in activity.tags" :key="tag.id">{{tag.tag_name}}</b-dropdown-item>
            </b-dropdown>
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
          <b-button v-b-modal.modal-1 @click="activity_id=activity.id">Add task</b-button>
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
    name: "activities",
    data(){
        return {
            activities: [],
            activity:{
              id:'',
              activity_name:'',
                priority:'low',
                tags:[],
            },
            item:{
                id:'',
                item_name:'',
                activity_id:'',
                status: 0,
                dead_line:'',
            },
            itemNameState: null,
            activity_id:'',
            pagination:{},
            edit:false,
            search:'',
            priorities:['low','medium','high','DANGER'],
            sort:'',
            label:'',
            tags:[],
            allValid:false,
            filter:'',
        }
    },
    created(){
        this.fetchActivities();
    },
    methods: {
        fetchActivities(page_url = '/api/v1/activities',sort='created_at') {
            let vm = this;
            this.fetchLabels();
            this.sort=sort;
            page_url = this.search!=''?'/api/v1/activities':page_url;
            fetch(page_url, {
                method: 'POST',
                body: JSON.stringify({'search':this.search,'order':sort,'filter':this.filter}),
                headers: {
                    "Content-Type": 'application/json'
                }
            })
                .then(res => res.json())
                .then(res => {
                    this.activities = res.data.data;
                    vm.makePagination(res.data);
                    let arr=[]
                    this.activities.forEach((a)=>{
                        a.items.forEach((a)=>{
                            arr.push(a.status);
                        })
                    });
                    this.allValid=arr.every(a=>a);
                })
                .catch(err => console.log(err))
        },
        makePagination(meta) {
            this.pagination = {
                current_page: meta.current_page,
                current_page_url: 'http://localhost:8000/api/v1/activities?page='+meta.current_page,
                last_page: meta.last_page,
                next_page_url: meta.next_page_url,
                prev_page_url: meta.prev_page_url
            };
        },
        deleteActivity(id) {
            if (confirm('Delete activity ' + id)) {
                fetch('api/v1/activities/' + id, {method: 'delete'})
                    .then(res => {
                        this.fetchActivities();
                    })
                    .then(data => {
                    })
                    .catch(err => console.log(err));
            }
        },
        deleteAll(){
            if (confirm('Delete all ')) {
                fetch('api/v1/activities/', {method: 'delete'})
                    .then(res => {
                        this.fetchActivities();
                    })
                    .then(data => {
                    })
                    .catch(err => console.log(err));
            }
        },
        addActivity() {
            if (!this.edit) {
                fetch('api/v1/activities/add', {
                    method: 'post',
                    body: JSON.stringify(this.activity),
                    headers: {
                        "Content-Type": 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                            this.activity.activity_name = '';
                            this.activity_id=data.data.id;
                            this.activity.tags.forEach((tag) => {isNaN(tag)?'':this.attachLabel(tag);})
                            this.activity_id='';
                            this.fetchActivities();
                            this.reset();
                        }
                    )
                    .catch(err => console.log(err))
            } else {
                fetch('api/v1/activities/' + this.activity_id, {
                    method: 'put',
                    body: JSON.stringify(this.activity),
                    headers: {
                        "Content-Type": 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                            this.activity.name = '';
                            this.activity.tags.forEach((tag) => {isNaN(tag)?'':this.attachLabel(tag);})

                            this.fetchActivities();
                            this.edit = false;
                            this.reset();
                        }
                    )
                    .catch(err => console.log(err))
            }
        },
        editActivity(activity) {
            this.edit = true;
            this.activity.id = activity.id;
            this.activity.priority = activity.priority;
            this.activity_id = activity.id;
            this.activity.tags = activity.tags;
            this.activity.activity_name = activity.activity_name;
        },
        validateTask(){
             fetch('api/v1/items/', {
                    method: 'put',
                    body: JSON.stringify({'status':!this.allValid}),
                    headers: {
                        "Content-Type": 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                            this.fetchActivities();
                        }
                    )
                    .catch(err => console.log(err))
        },
        checkFormValidity() {
            const valid = this.$refs.form.checkValidity()
            this.itemNameState = valid
            return valid
        },
        resetModal() {
            this.item.item_name = ''
            this.itemNameState = null
        },
        resetModal1() {
            this.edit=false;
            this.activity.activity_name=this.activity_id='';
        },
        handleOk(bvModalEvt) {
            // Prevent modal from closing
            bvModalEvt.preventDefault()
            // Trigger submit handler
            this.handleSubmit()
        },
        handleSubmit() {
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return
            }
            // Push the name to submitted names
            this.addTask();
            // Hide the modal manually
            this.$nextTick(() => {
                this.$bvModal.hide('modal-1')
            })
        },
        addTask() {
            this.item.dead_line = this.formatDate(this.item.dead_line);
            if (!this.edit) {
                fetch('api/v1/activities/' + this.activity_id + "/items", {
                    method: 'post',
                    body: JSON.stringify(this.item),
                    headers: {
                        "Content-Type": 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                            this.item.id = '';
                            this.item.item_name = '';
                            this.item.activity_id = '';
                            this.item.status = 0;
                            this.item.dead_line = '';
                            this.fetchActivities();
                        }
                    )
                    .catch(err => console.log(err))
            } else {
                fetch('api/v1/activities/' + this.activity_id + '/items/' + this.item.id, {
                    method: 'put',
                    body: JSON.stringify(this.item),
                    headers: {
                        "Content-Type": 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                            this.item.id = '';
                            this.item.item_name = '';
                            this.item.activity_id = '';
                            this.item.status = 0;
                            this.item.dead_line = '';
                            this.fetchActivities();
                            this.edit = false;
                        }
                    )
                    .catch(err => console.log(err))
            }
        },
        deleteTask(id, activityId) {
            if (confirm('Delete Task ' + id)) {
                fetch('api/v1/activities/' + activityId + '/items/' + id, {method: 'delete'})
                    .then(res => {
                        alert('Task deleted');
                        this.fetchActivities();
                    })
                    .then(data => {
                    })
                    .catch(err => console.log(err));
            }
        },
        editTask(task) {
            this.edit = true;
            this.item.item_name = task.item_name;
            this.item.id = task.id;
            this.activity_id = task.activity_id;
            this.item.dead_line = task.dead_line;
            this.$bvModal.show('modal-1')
        },
        updateStatus(task) {
            fetch('api/v1/activities/' + task.activity_id + '/items/' + task.id, {
                method: 'put',
                body: JSON.stringify(task),
                headers: {
                    "Content-Type": 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                        this.activity.name = '';
                        this.fetchActivities();
                        this.edit = false;
                    }
                )
                .catch(err => console.log(err))
        },
        formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [year, month, day].join('-');
        },
        isPassed(date) {
            let d = new Date();
            let d1 = new Date(date);
            return d1 < d;
        },
        show(activity){
          return !activity.items.some(e => this.isPassed(e.dead_line));
        },
        addLabel(){
            fetch('api/v1/tags/add', {
                    method: 'post',
                    body: JSON.stringify({'tag_name':this.label}),
                    headers: {
                        "Content-Type": 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                            this.label = '';
                            this.fetchLabels();

                        }
                    )
                    .catch(err => console.log(err))
        },
        fetchLabels(){
                fetch('/api/v1/tags', {
                method: 'GET'
            })
                .then(res => res.json())
                .then(res => {
                    this.tags = res.data;
                })
                .catch(err => console.log(err))
        },
        deleteLabel(labelId){
            if (confirm('Delete tag ' + labelId)) {
                fetch('api/v1/tags/' + labelId , {method: 'delete'})
                    .then(res => {
                        this.fetchActivities();
                    })
                    .then(data => {
                    })
                    .catch(err => console.log(err));
            }
        },
        attachLabel(labelId){
            fetch('api/v1/activities/'+ this.activity_id+'/tags/'+labelId, {
                    method: 'post'
                })
                    .then(data => {;
                            this.fetchLabels();
                        }
                    )
                    .catch(err => console.log(err));
                    this.fetchActivities();
                    this.fetchLabels();
        },
         containsObject(obj) {
            var i;
            let list=this.activity.tags;
            for (i = 0; i < list.length; i++) {
                if (list[i].id == obj.id) {
                    return true;
                }
            }
            return false;
        },
        reset(){
            this.activity.id='';
            this.activity.activity_name='';
            this.activity.priority='low';
            this.activity.tags=[];
            this.activity_id='';
            document.querySelectorAll('.checks').forEach((a)=>{a.removeAttribute('checked')})
        },
        pushTo(id){
            this.activity.tags.indexOf(id)==-1?this.activity.tags.push(id):this.activity.tags.splice(this.activity.tags.indexOf(id)!=-1,1);
        },
        filterBy(id){
            this.filter=id;
            this.fetchActivities();
        }

    }
}
</script>

<style scoped>

:root {
    font-size: calc(16px + (24 - 16) * (100vw - 320px) / (1920 - 320));
}
body,
button,
#search {
    font: 1em Hind, sans-serif;
    line-height: 1.5em;
}
body,
input {
    color: #171717;
}
body,
.search-bar {
    display: flex;
}
body {
    background: #f1f1f1;
    height: 100vh;
}
.search-bar input,
.search-btn,
.search-btn:before,
.search-btn:after {
    transition: all 0.25s ease-out;
}
.search-bar input,
.search-btn {
    width: 3em;
    height: 3em;
}
.search-bar input:invalid:not(:focus),
.search-btn {
    cursor: pointer;
}
.search-bar,
.search-bar input:focus,
.search-bar input:valid {
    width: 100%;
}
.search-bar #search:focus,
.search-bar #search:not(:focus) + .search-btn:focus {
    outline: transparent;
}
.search-bar {
    margin: auto;
    padding: 1.5em;
    justify-content: center;
    max-width: 30em;
}
.search-bar #search {
    background: transparent;
    border-radius: 1.5em;
    box-shadow: 0 0 0 0.4em #171717 inset;
    padding: 0.75em;
    transform: translate(0.5em, 0.5em) scale(0.5);
    transform-origin: 100% 0;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
.search-bar #search::-webkit-search-decoration {
    -webkit-appearance: none;
}
.search-bar #search:focus,
.search-bar #search:valid {
    background: #fff;
    border-radius: 0.375em 0 0 0.375em;
    box-shadow: 0 0 0 0.1em #d9d9d9 inset;
    transform: scale(1);
}
.search-btn {
    background: #171717;
    border-radius: 0 0.75em 0.75em 0 / 0 1.5em 1.5em 0;
    padding: 0.75em;
    position: relative;
    transform: translate(0.25em, 0.25em) rotate(45deg) scale(0.25, 0.125);
    transform-origin: 0 50%;
}
.search-btn:before,
.search-btn:after {
    content: "";
    display: block;
    opacity: 0;
    position: absolute;
}
.search-btn:before {
    border-radius: 50%;
    box-shadow: 0 0 0 0.2em #f1f1f1 inset;
    top: 0.75em;
    left: 0.75em;
    width: 1.2em;
    height: 1.2em;
}
.search-btn:after {
    background: #f1f1f1;
    border-radius: 0 0.25em 0.25em 0;
    top: 51%;
    left: 51%;
    width: 0.75em;
    height: 0.25em;
    transform: translate(0.2em, 0) rotate(45deg);
    transform-origin: 0 50%;
}
.search-btn span {
    display: inline-block;
    overflow: hidden;
    width: 1px;
    height: 1px;
}
/* Active state */
.search-bar #search:focus + .search-btn,
.search-bar #search:valid + .search-btn {
    background: #008cff;
    border-radius: 0 0.375em 0.375em 0;
    transform: scale(1);
}
.search-bar #search:focus + .search-btn:before,
.search-bar #search:focus + .search-btn:after,
.search-bar #search:valid + .search-btn:before,
.search-bar #search:valid + .search-btn:after {
    opacity: 1;
}
.search-bar #search:focus + .search-btn:hover,
.search-bar #search:valid + .search-btn:hover,
.search-bar #search:valid:not(:focus) + .search-btn:focus {
    background: #0c48db;
}
.search-bar #search:focus + .search-btn:active,
.search-bar #search:valid + .search-btn:active {
    transform: translateY(1px);
}
@media screen and (prefers-color-scheme: dark) {
    body,
    #search {
        color: #f1f1f1;
    }
    body {
        background: #171717;
    }
    .search-bar #search {
        box-shadow: 0 0 0 0.4em #3a3838 inset;
    }
    .search-bar #search:focus,
    .search-bar #search:valid {
        background: #3d3d3d;
        box-shadow: 0 0 0 0.1em #3d3d3d inset;
    }
    .search-btn {
        background: #3a3838;
    }
}
</style>
