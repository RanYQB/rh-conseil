import React, {useEffect, useState} from "react";
import {usePaginatedFetch} from "../hooks/hooks";
import {render, unmountComponentAtNode} from "react-dom";



const Offers = ({keyword, city}) =>{
    const {items: offers, load, loading, count, hasMore} = usePaginatedFetch('api/offers?slug=' + keyword + '&City.label=' + city)
    const [offerSelected, setOfferSelected] = useState([])

    useEffect(() => {load()}, [])
/*
    if(offers.length >= 1){
        setFirst(offers.slice(0,1).find(o => o))
    }*/

    console.log(offers.slice(0,1))

    return <div className="home-offers">
        <div>
            {loading && 'chargement ...'}
            <Title count={count}/>
            {offers.map(offer => <Offer key={offer.id} offer={offer} setOfferSelected={setOfferSelected} className="offers-list"/>) }
            {hasMore && <button disabled={loading} onClick={load}>Charger plus d'offres</button>}
        </div>
        <div>
            {count >= 1 && offers.length >= 1 ? <OfferCard offer={offerSelected ? offerSelected : offers.slice(0,1).find(o => o)}/> : '' }
        </div>
    </div>
}

function Title({count}){

    return <div> {count >=1 ? <h3>{count} offre{count > 1 ? 's' : '' }</h3> : <h3>Aucune offre disponible</h3>}
    </div>
}

const Offer = React.memo( ({offer, setOfferSelected}) => {


    const date = new Date(offer.published_at)
    return <div className="offer" >
        <h3 onClick={() => { setOfferSelected(offer)}}>{offer.title} H/F</h3>
        <p><strong>{offer.recruiter.name}</strong></p>
        <p>{offer.description}</p>
        <p>Salaire : {offer.salary}</p>
        <p>Type de contrat : {offer.contrat_type}</p>
        <p>Nombre de postes à pourvoir : {offer.positions}</p>
        <p>Lieu : {offer.City.name}</p>
        <p>Date de publication : {date.toLocaleString(undefined,)}</p>
    </div>
})

function OfferCard({offer}){


    return <div className="offer" >
        <h3>Text</h3>
        <p>{offer.id}</p>

    </div>
}

class OffersList extends HTMLElement{

    connectedCallback () {

        const keywords = this.dataset.keyword
        const cityLabel = this.dataset.city

        render(<div className="offers-results">
            <Offers keyword={keywords} city={cityLabel}/>

        </div>, this)
    }
    disconnectedCallback(){
        unmountComponentAtNode(this)
    }
}

customElements.define('offers-list', OffersList)