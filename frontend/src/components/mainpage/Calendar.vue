 <template>
  <div class="calendar-container">
    <vue-cal 
      class="vuecal--blue-theme"
      active-view="month"
      :events="combineTodo"
    />
    </div>
  </template>

<script>
  import VueCal from 'vue-cal'
  import 'vue-cal/dist/vuecal.css'
  import { Db2Cal } from '../../convertTodo.js';
  import { GetTodoFromDb } from '../../fetch.js';

  export default {
    emits: ['fetchedTodo'],
    components: {
      VueCal,
    },
    props: {
      newTodo: {
        type: Array,
        Required: true
      }
    },
    data: () => {
      return {
        fetchedEvents: [],
        testData: [
          { start_date: '2024-12-11', due_date: '2024-12-15', title: 'Event 1' , description: '<i class="icon material-icons">shopping_cart</i>', class: 'leisure', completed: false},
        ],
      }
    },
    methods: {
      async fetchTodo(){
        this.fetchedEvents = await GetTodoFromDb();
        this.fetchedEvents.forEach(todo => {
          todo.completed = !!todo.completed;
        })
        return;
      },
      sendToMain(){
        this.$emit('fetchedTodo', this.fetchedEvents);
      }
    },
    computed: {
      combineTodo(){
        return Db2Cal([...this.fetchedEvents, ...this.newTodo], this.fetchedEvents.length);
      }
    },
    mounted() {
      this.fetchTodo();
    },
    watch: {
      fetchedEvents: {
        handler(newValue){
          this.sendToMain();
        }, 
        deep: true
      }
    }
  }
</script>

<style>
.calendar-container {
  margin: 0 auto; 
  width: 100%;
}

.vuecal__event.upcoming {
  background-color: rgba(66, 231, 253, 0.7) !important; 
  border: 1px solid rgba(66, 231, 253, 0.5) !important;
  color: #fff !important;
}
/* .vuecal__event.complete {
  background-color: rgba(66, 231, 253, 0.7) !important; 
  border: 1px solid rgba(66, 231, 253, 0.5) !important;
  color: #fff !important;
} */
.vuecal__event.new {
  background-color: rgba(94, 253, 66, 0.7) !important; 
  border: 1px solid rgba(94, 253, 66, 0.5) !important;
  color: #fff !important;
}
.vuecal__event.endIn3Days {
  background-color: rgba(255, 200, 0, 0.7) !important; 
  border: 1px solid rgba(255, 200, 0, 0.5) !important;
  color: #fff !important;
}
.vuecal__event.endInWeek {
  background-color: rgba(253, 119, 66, 0.7) !important; 
  border: 1px solid rgba(253, 119, 66, 0.5) !important;
  color: #fff !important;
}
.vuecal__event.overDue {
  background-color: rgba(255, 0, 0, 0.7) !important; 
  border: 1px solid rgba(255, 0, 0, 0.5) !important;
  color: #fff !important;
}

.vuecal__cell-events-count {
  background-color: rgba(255, 0, 0, 1) !important;
}


</style>

