//запрос на бронирование
import axios from "axios";

export const makeOrderBook = async (values:any) => {
    return await axios.post('/api/book', values);
}

//запрос на подтверждение бронирования
export const makeOrderApprove = async (barcode: number) => {
    return await axios.post('/api/approve', {barcode});
}
