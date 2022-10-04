
import React, {useState} from "react";
import {render, unmountComponentAtNode} from "react-dom";
import AsyncSelect from "react-select/async";

function SelectCities(){
    const [cities, setCities] = useState([])

    const handleChange = (selectedOption) =>{
        console.log("handleChange", selectedOption);
    }


    const loadOptions = async (inputText, callback) => {
        console.log(inputText)
        const response = await fetch(`api/cities?label=${inputText}`, {
            headers: {
                'Accept': 'application/ld+json'
            }
        })
        const responseData = await response.json()
        if(response.ok){
            setCities(responseData['hydra:member'])
            console.log(cities)
        } else {
            console.error(responseData)
        }

        const uniqCities = [...cities.reduce((map, obj) => map.set(obj.label, obj ), new Map()).values()]

        const filteredOptions = uniqCities.filter(city => city.label.toLowerCase().includes(inputText.toLowerCase()))

        callback(filteredOptions)
    };

    console.log(loadOptions)

    return (
        <AsyncSelect
            placeholder={'Paris 01'}
            loadOptions={loadOptions}
            onChange={handleChange}
            defaultOptions={false}
            getOptionLabel={e => e.label.charAt(0).toUpperCase() + e.label.slice(1)}
            getOptionValue={e => e.id}
        />
    )

}

class CityField extends HTMLElement {


    connectedCallback(){
        render(<SelectCities/>, this)
    }

    disconnectedCallback(){
        unmountComponentAtNode(this)
    }


}

customElements.define('city-field', CityField)







