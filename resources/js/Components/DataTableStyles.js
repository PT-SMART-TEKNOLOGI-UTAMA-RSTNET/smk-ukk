import { defaultThemes } from "react-data-table-component";
export const conditionalNilaiTugas = [
    {
        when: row => row.meta.nilai.label < row.meta.learning.kkm,
        style: {
            backgroundColor: 'rgba(255, 171, 0, 0.5)',
            color: 'rgb(221,44,0)',
            fontWeight:'bold',
        },
    },
    {
        when: row => row.meta.nilai.label >= row.meta.learning.kkm,
        style: {
            backgroundColor: 'white',
            color: 'black',
        },
    },
];
export const compactGrid = {
    header: {
        style: {
            minHeight: '56px',
        },
    },
    headRow: {
        style: {
            borderTopStyle: 'solid',
            borderTopWidth: '1px',
            borderTopColor: defaultThemes.default.divider.default,
        },
    },
    headCells: {
        style: {
            '&:not(:last-of-type)': {
                borderRightStyle: 'solid',
                borderRightWidth: '1px',
                borderRightColor: defaultThemes.default.divider.default,
            },
            fontSize: '14px',
            fontWeight: 'bold'
        },
    },
    cells: {
        style: {
            '&:not(:last-of-type)': {
                borderRightStyle: 'solid',
                borderRightWidth: '1px',
                borderRightColor: defaultThemes.default.divider.default,
            },
        },
    },

};
export const primaryTable = {
    headRow: {
        style: {
            borderTopStyle: 'solid',
            borderTopWidth: '1px',
            backgroundColor : "#757ce8",
            fontWeight : "bold",
            color : 'white',
            borderTopColor: defaultThemes.default.divider.default,
        },
    },
    headCells: {
        style: {
            '&:not(:last-of-type)': {
                borderRightStyle: 'solid',
                borderRightWidth: '1px',
                borderRightColor: defaultThemes.default.divider.default,
            },
        },
    },
    cells: {
        style: {
            backgroundColor : "white",
            '&:not(:last-of-type)': {
                borderRightStyle: 'solid',
                borderRightWidth: '1px',
                borderRightColor: defaultThemes.default.divider.default,
            },
        },
    },

};
