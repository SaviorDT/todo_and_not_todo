 <template>
  <div class="calendar-container">
    <vue-cal 
      class="vuecal--blue-theme"
      active-view="month"
      :events="events"
      />
    </div>
  </template>
    <!-- events-on-month-view="short" -->

<script>
import { ref } from 'vue'
import VueCal from 'vue-cal'
import 'vue-cal/dist/vuecal.css'

export default {
  components: {
    VueCal,
  },
  data: () => {
    return {
      events: [
        { start: '2024-12-11', end: '2024-12-15', title: 'Event 1' , content: '<i class="icon material-icons">shopping_cart</i>', class: 'leisure'},
      ],
    }
  },
  methods: {
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

