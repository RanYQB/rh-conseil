import {useState} from "react";
import {useCallback} from "react";

export function usePaginatedFetch(url){
    const [loading, setLoading] = useState(false)
    const [items, setItems] = useState([])
    const [count, setCount] = useState(0)
    const [next, setNext] = useState(null)
    const load = useCallback(async ()=>{
        setLoading(true)
        const response = await fetch(next || url, {
            headers: {
                'Accept': 'application/ld+json'
            }
        })
        const responseData = await response.json()
        if(response.ok){
            setItems(items => [...items, ...responseData['hydra:member']] )
            setCount(responseData['hydra:totalItems'])
            if(responseData['hydra:view'] && responseData['hydra:view']['hydra:next']){
                setNext(responseData['hydra:view']['hydra:next'])
            } else {
                setNext(null)
            }
        } else {
            console.error(responseData)
        }
        setLoading(false)
    }, [url, next])
    return {items, load, loading, count, hasMore: next !== null}

}


export function useFetch(url){
    const [loading, setLoading] = useState(false)
    const [items, setItems] = useState([])
    const load = useCallback(async ()=>{
        setLoading(true)
        const response = await fetch(next, {
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