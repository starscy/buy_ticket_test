export type EventType = {
    id: number,
    name: string,
    description: string,
    prices: {
        adult: number,
        kid: number
    },
    date: string,
    types?: EventTypesType[]
};

type EventTypesType = {
    id: number,
    name: string,
    pivot: {
        "event_id": number,
        "event_type_id": number,
        "prices": string
    }
};
