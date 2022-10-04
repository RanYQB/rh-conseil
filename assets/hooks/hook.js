import {useCallback, useState} from "react";

export function useFetch (url){
    const [loading, setLoading] = useState(false)
    const [items, setItems] = useState([])
    const load = useCallback(async ()=>{
        setLoading(true)
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/ld+json'
            }
        })
        const responseData = await response.json()
        if(response.ok){
            setItems(responseData['hydra:member'])
        } else {
            console.error(responseData)
        }
        setLoading(false)
    }, [url])
    return {items, load, loading}

}