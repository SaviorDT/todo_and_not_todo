import dayjs from "dayjs";

export function Db2Cal(events, fetchedLength){
    const temList = events.map((event, index) => {
        let className = 'upcoming';
        if(event.completed)className = 'completed';
        else if(index >= fetchedLength)className = 'new';
        else if(dayjs(event.due_date).diff(dayjs(), 'hour') <= 0)className = 'overDue';
        else if(dayjs(event.due_date).diff(dayjs(), 'hour') <= 72)className = 'endIn3Days';
        else if(dayjs(event.due_date).diff(dayjs(), 'hour') <= 168)className = 'endInWeek';

        return {
            start: dayjs(event.start_date).format('YYYY-MM-DD HH:mm'),
            end: dayjs(event.due_date).format('YYYY-MM-DD HH:mm'),
            title: event.title,
            content: event.description,
            completed: event.completed,
            class: className
        }
    })
    return temList == '' ? [] : temList;
}

export function Cal2Db(events){
    const temList = events.map(event => {
        return {
            start_date: dayjs(event.start).format('YYYY-MM-DD HH:mm'),
            due_date: dayjs(event.end).format('YYYY-MM-DD HH:mm'),
            title: event.title,
            description: event.content,
            completed: event.completed
        }
    })
    return temList == '' ? [] : temList;
}

export function ChangeTimeFormat(date){
    return dayjs(date).format('YYYY-MM-DD HH:mm')
}