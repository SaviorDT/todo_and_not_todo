import dayjs from "dayjs";

export function Db2Cal(events){
    const temList = events.map(event => {
        return {
            start: dayjs(event.start_date).format('YYYY-MM-DD HH:mm'),
            end: dayjs(event.due_date).format('YYYY-MM-DD HH:mm'),
            title: event.title,
            content: event.description,
        }
    })
    return temList == '' ? [] : temList;
}

export function Cal2Db(events){
    const temList = events.map(event => {
        return {
            start_date: event.start,
            due_date: event.end,
            title: event.title,
            description: event.content
        }
    })
    return temList == '' ? [] : temList;
}