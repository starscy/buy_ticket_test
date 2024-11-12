import React, {useState} from "react";
import {EventType} from "../../Types/types";
import {Inertia} from '@inertiajs/inertia'
import {makeOrderApprove, makeOrderBook} from "../../Api/api";

type EventProps = {
    eventData: EventType;
}

interface FormValuesProps {
    id: number;
    adult_ticket_count: number;
    kid_ticket_count: number;
    barcode: number;

    [key: string]: any; // Для динамических свойств
}

/**
 * Форма покупки билетов
 *
 * @param {EventType} eventData
 * @constructor
 */
const EventForm: React.FC<EventProps> = ({eventData}) => {
    const [error, setError] = useState(null)

    const [values, setValues] = useState<FormValuesProps>({
        id: eventData.id,
        adult_ticket_count: 0,
        kid_ticket_count: 0,
        barcode: Math.floor(Math.random() * (9999 - 1000 + 1)) + 1000
    })

    function handleChange(e: React.ChangeEvent<HTMLInputElement>) {
        const key = e.target.id;
        const value = e.target.value
        setValues(values => ({
            ...values,
            [key]: value,
        }))
    }

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        const maxRetries = 10;
        let attempt = 0;
        let success = false;

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
            try {
                await makeOrderApprove(values.barcode);
                setError(null);
            } catch (error) {
                console.error(`Ошибка при подтверждении бронирования`, error);
            }
        }

        Inertia.post('/orders', values)
    };


    return (
        <div className="p-4 mb-12">
            <form className="h-full" onSubmit={handleSubmit} method="POST">
                <div
                    className="flex rounded-md flex-col justify-between h-full p-4 m-4 border border-2 hover:bg-gray-100 cursor-pointer">
                    <div>
                        <h3 className={'flex items-center justify-between'}><span
                            className={'text-lg font-bold'}>Название:</span> {eventData.name}</h3>
                        <p className={'flex items-center justify-between mb-3'}><span
                            className={'text-lg font-bold'}>Описание:</span>{eventData.description}</p>
                        <div className={'flex gap-6 justify-between items-center mb-6'}>
                            <label className={"grow-1"}><span
                                className={'text-sm font-bold'}>Цена взрослого - </span>{eventData.prices.adult}</label>
                            <input type="number"
                                   id="adult_ticket_count"
                                   name="adult_ticket_count"
                                   min={0}
                                   value={values.adult_ticket_count}
                                   onChange={handleChange}
                                   className={"w-20 rounded-md"}
                            />
                        </div>
                        <div className={'flex justify-between items-center mb-6'}>
                            <label><span className={'text-sm font-bold'}>Цена детского - </span>{eventData.prices.kid}
                            </label>
                            <input type="number"
                                   min={0}
                                   id="kid_ticket_count"
                                   name="kid_ticket_count"
                                   value={values.kid_ticket_count}
                                   onChange={handleChange}
                                   className={"w-20 rounded-md"}
                            />
                        </div>

                        {eventData.types && eventData.types.map(type => (
                            <div key={type.id} className={'flex justify-between items-center mb-6'}>
                                <label htmlFor={`ticket_count_${type.id}`}>
                                <span
                                    className={'text-sm font-bold'}>{type.name}-</span> {JSON.parse(type.pivot.prices).price}
                                </label>
                                <input
                                    type="number"
                                    id={`ticket_count_${type.id}`}
                                    value={values[`ticket_count_${type.id}`] || 0}
                                    min={0}
                                    onChange={handleChange}
                                    className={"w-20 rounded-md"}
                                />
                            </div>
                        ))}
                    </div>
                    <div className={'flex flex-col'}>
                        <button type="submit"
                                className="inline-block rounded px-6 pb-2 pt-2.5 text-xs font-medium uppercase bg-gray-800 text-white hover:bg-red-500">Купить
                        </button>
                        {
                            error &&
                            <p className="text-red-600">Выберите колличество билетов</p>
                        }
                    </div>
                </div>
            </form>
        </div>
    );
}

export default EventForm;
