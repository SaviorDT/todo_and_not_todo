 <template>
  <div class="calendar-container">
    <vue-cal 
      class="vuecal--blue-theme"
      active-view="month"
      :events="combineTodo"
    />
      <!-- events-on-month-view="short" -->
    </div>
  </template>

<script>
  import VueCal from 'vue-cal'
  import 'vue-cal/dist/vuecal.css'
  import { Db2Cal, Cal2Db } from '../../convertTodo.js';

  export default {
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
        storedEvents: [
          { start: '2024-12-11', end: '2024-12-15', title: 'Event 1' , content: '<i class="icon material-icons">shopping_cart</i>', class: 'leisure'},
        ],
      }
    },
    computed: {
      combineTodo(){
        console.log([...this.newTodo, ...this.storedEvents]);
        return [...this.newTodo, ...this.storedEvents];
      }
    }

  }
</script>

<style>
.calendar-container {
  flex-grow: 1;
  padding: 20px;
  background-color: #fff;
}

.vuecal__event.leisure {
  background-color: rgba(253, 156, 66, 0.9) !important; 
  border: 1px solid rgb(233, 136, 46) !important;
  color: #fff !important;
}

.vuecal__cell-events-count {
  background-color: rgba(255, 0, 0, 1) !important;
}


</style>

