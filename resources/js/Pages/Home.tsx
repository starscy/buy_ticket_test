import React from "react";
import {EventType} from "../Types/types";
import EventForm from "../Components/event/EventForm";
import Header from "../Components/Header";

type HomeProps = {
    events: EventType[],
    auth: any
}

/**
 * Домашняя страница, показывает, события для путешествий
 *
 * @param events
 * @param auth
 * @constructor
 */
const Home: React.FC<HomeProps> = ({events, auth}) => {
    return (
        <div className="p-2">
            <Header auth={auth}/>
            <h1 className={"font-bold text-center text-xl mb-6"}>Покупка билетов</h1>


            {/*тут показаны все события, куда можно поехать*/}
            <div className={'flex flex-wrap'}>
                {
                    events.map((el) => (
                        <EventForm key={el.id} eventData={el}/>
                    ))
                }
            </div>
        </div>
    );
}

export default Home;
