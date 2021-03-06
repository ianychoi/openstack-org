import React, { Component } from 'react'

class MainFilterFields extends Component {

    componentDidMount() {
        // React's onChange doesn't play well with chosen so use jQuery.
        $(this.refs.ddl_track_groups).chosen({ width: '100%' })
        $(this.refs.ddl_track_groups).on('change', e => route(e, 'track_groups'))
        $(this.refs.ddl_event_types).chosen({ width: '100%' })
        $(this.refs.ddl_event_types).on('change', e => route(e, 'event_types'))
        $(this.refs.ddl_tracks).chosen({ width: '100%' })
        $(this.refs.ddl_tracks).on('change', e => route(e, 'tracks'))
        $(this.refs.ddl_tags).chosen({ width: '100%' })
        $(this.refs.ddl_tags).on('change', e => route(e, 'tags'))
        $(this.refs.ddl_levels).chosen({ width: '100%' })
        $(this.refs.ddl_levels).on('change', e => route(e, 'levels'))

        const { setFilters } = this.props

        const route = (e, filterName) => setFilters({
            [filterName]: $(e.currentTarget).val()
        })
    }

    componentDidUpdate(prevProps, prevState) {
        $(this.refs.ddl_track_groups).trigger("chosen:updated")
        $(this.refs.ddl_event_types).trigger("chosen:updated")
        $(this.refs.ddl_tracks).trigger("chosen:updated")
        $(this.refs.ddl_tags).trigger("chosen:updated")
        $(this.refs.ddl_levels).trigger("chosen:updated")
    }

    render() {
        const {
            ScheduleProps: { summit },
            filters: { values, expanded, allowedTracks }
        } = this.props;

        return (
        <div id="all-events-filter-wrapper" style={{display: 'block'}}
        className={`row ${expanded ? '' : 'hide'}`}>
            <div className="col-sm-12">
                <a href={summit.track_list_link} target="_blank">
                    Learn more about the {summit.title} Summit Categories and Tracks.
                </a>
            </div>
            <div className="col-sm-15 col-xs-12 single-filter-wrapper">
                <select ref="ddl_track_groups" name="ddl_track_groups"
                size="5" multiple="multiple" data-placeholder="Summit Categories"
                value={values.track_groups || []} onChange={() => false}>
                    {Object.keys(summit.category_groups).map(groupId => (
                    <option key={groupId} value={groupId}>
                        {summit.category_groups[groupId].name}
                    </option>
                    ))}
                 </select>
            </div>
            <div className="col-sm-15 col-xs-12 single-filter-wrapper">
                <select ref="ddl_tracks" multiple="multiple" data-placeholder="Tracks"
                value={values.tracks || []} onChange={() => false}>
                    {summit.track_ids.map(trackId => (
                    <option key={trackId} value={trackId}
                    className={allowedTracks.indexOf(trackId) < 0 ? 'hide' : ''}>
                        {summit.tracks[trackId].name}
                    </option>
                    ))}
                </select>
            </div>
            <div className="col-sm-15 col-xs-12 single-filter-wrapper hide">
                <select ref="ddl_event_types" name="ddl_event_types"
                size="7" multiple="multiple" data-placeholder="Event Types"
                value={values.event_types || []} onChange={() => false}>
                    {summit.event_type_ids.map(typeId => (
                    <option key={typeId} value={typeId}>
                        {summit.event_types[typeId].type}
                    </option>
                    ))}
                </select>
            </div>
            <div className="col-sm-15 col-xs-12 single-filter-wrapper">
                <select ref="ddl_levels" multiple="multiple" data-placeholder="Presentation Level"
                value={values.levels || []} onChange={() => false}>
                    {Object.keys(summit.presentation_levels).map(levelId => (
                    <option key={levelId} value={levelId.toLowerCase()}>
                        {summit.presentation_levels[levelId].level}
                    </option>
                    ))}
                </select>
            </div>
            <div className="col-sm-15 col-xs-12 single-filter-wrapper">
                <select ref="ddl_tags" multiple="multiple" data-placeholder="Tags"
                value={values.tags || []} onChange={() => false}>
                    {summit.tag_ids.map(tagId => (
                    <option key={tagId} value={tagId}>
                        {summit.tags[tagId].name}
                    </option>
                    ))}
                </select>
            </div>
        </div>
    )}
}

MainFilterFields.propTypes = {
    filters: React.PropTypes.object.isRequired,
    setFilters: React.PropTypes.func.isRequired,
    ScheduleProps: React.PropTypes.object.isRequired,
}

export default MainFilterFields
