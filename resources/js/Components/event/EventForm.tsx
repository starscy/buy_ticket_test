import React, {useState} from "react";
import {EventType} from "../../Types/types";
import {Inertia} from '@inertiajs/inertia'
import axios from "axios";

type EventProps = {
    eventData: EventType;
}

/**
 * Форма покупки билетов
 *
 * @param {EventType} eventData
 * @constructor
 */
const EventForm: React.FC<EventProps> = ({eventData}) => {
    const [error, setError] = useState(null)

    const [values, setValues] = useState({
        id: eventData.id,
        adult_ticket_count: 0,
        kid_ticket_count: 0,
        barcode: Math.floor(Math.random() * (9999 - 1000 + 1)) + 1000
    })

    function handleChange(e) {
        const key = e.target.id;
        const value = e.target.value
        console.log('value', value)
        setValues(values => ({
            ...values,
            [key]: value,
        }))
    }


    // @ts-ignore
    const handleSubmit = async (e) => {
        e.preventDefault();
        const maxRetries = 10;
        let attempt = 0;
        let success = false;

        // if (values.adult_ticket_count <= 0 && values.kid_ticket_count <= 0) {
        //     setError('Колличество билетов не выбрано');
        //     return;
        // }



        while (attempt < maxRetries && !success) {
            try {
                await makeOrderBook(values);
                success = true;
            } catch (error) {
                attempt++;
                console.error(`Ошибка бронирования`, error);

                setValues(values => ({
                    ...values,
                    ['barcode']: Math.floor(Math.random() * (9999 - 1000 + 1)) + 1000,
                }))

                if (attempt === maxRetries) {
                    console.error('Max retries reached. Request failed.');
                }
            }
        }

        if (success) {
            let result = await makeOrderApprove(values.barcode);
            console.log('result', result)
            setError(null);
        }


        Inertia.post('/orders', values)
    };

    // @ts-ignore
    //запрос на бронирование
    const makeOrderBook = async (values) => {
        return await axios.post('/api/book', values);
    }

    //запрос на подтверждение бронирования
    const makeOrderApprove = async (barcode: number) => {
        return await axios.post('/api/approve', {barcode});
    }

    return (
        <div className="flex flex-col p-4 mb-12">
            <form onSubmit={handleSubmit} method="POST">
                <div className="flex p-4 flex-col m-4 border border-2 hover:bg-gray-100 cursor-pointer">
                    <h3>{eventData.name}</h3>
                    <p>{eventData.description}</p>
                    <p>Цена взрослого - {eventData.prices.adult}</p>
                    <input type="number"
                           id="adult_ticket_count"
                           name="adult_ticket_count"
                           value={values.adult_ticket_count}
                           onChange={handleChange}
                    />
                    <p>Цена детского - {eventData.prices.kid}</p>
                    <input type="number"
                           id="kid_ticket_count"
                           name="kid_ticket_count"
                           value={values.kid_ticket_count}
                           onChange={handleChange}/>
                        {eventData.types.map(type => (
                            <div key={type.id}>
                                <label htmlFor={`ticket_count_${type.id}`}>
                                    {type.name}: {JSON.parse(type.pivot.prices).price}
                                </label>
                                <input
                                    type="number"
                                    id={`ticket_count_${type.id}`}
                                    value={values[`ticket_count_${type.id}`] || 0}
                                    onChange={handleChange}
                                />
                            </div>
                        ))}
                </div>
                <button type="submit" className="button hover:bg-red-500">Купить</button>
                {
                    error &&
                    <p className="text-red-600">Выберите колличество билетов</p>
                }
            </form>
        </div>

    );
}

export default EventForm;
