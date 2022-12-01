import React, {Component} from 'react';
import TextField from '@mui/material/TextField';
import Autocomplete from '@mui/material/Autocomplete';
import ReactDOM from 'react-dom';

class SearchCityField extends Component {
    constructor(props) {
        super(props);
        this.state = {
            search: null,
            options: [],
            selectedOption: '',
        }
        this.getOptions = this.getOptions.bind(this)
        this.handleChange = this.handleChange.bind(this)

    }



    handleChange(event){
        this.setState( () =>({
            search: event.target.value
        }))
        this.getOptions()
    }

    getOptions() {
        const fetchURL = `api/cities?label=${this.state.search}`;
        fetch(fetchURL).then(response => {
            response.json().then(data => {
                    const results = data['hydra:member'];
                    results.filter(city => city.name.includes(this.state.search))
                    const optionsArray = []
                    results.map(v => (optionsArray.push({name: v.name, id: v.id})));
                    console.log(optionsArray);
                    this.setState({options: optionsArray });
                })

            })


    }

    render(){
        console.log(this.state.value)
        return (
            <Autocomplete
                disablePortal
                id="combo-box-demo"
                options={ !this.state.options ? [{label:"Loading...", id:0}] : this.state.options}
                getOptionLabel={(option) => option.name || ''}
                sx={{ width: 300 }}
                renderInput={(params) => <TextField {...params} label='Ville' key={(params)=> params.id  || ''} />}
                value={this.state.search}
                onChange={this.handleChange}

            />
        );
    }
}

ReactDOM.render(<SearchCityField/>, document.getElementById('home-search'));
