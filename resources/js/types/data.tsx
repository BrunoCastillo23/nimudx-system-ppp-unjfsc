// Nota: la jerarquía Facultad→Escuela→Sección vive en `types/models.ts`
// (interfaces `Faculty`, `School`, `Section`). Antes existía aquí una interfaz
// `Faculties` duplicada con una forma ligeramente distinta (sin `status`) que
// ningún componente importaba; se eliminó para evitar el tipado divergente.

export interface Supervision {
    id: number;
    module_id: number;
    approval_status: number;
}

export interface StudentSupervision {
    id: number;
    user: {
        email: string;
        person: { names: string; surnames: string };
    };
    section: {
        name: string;
        school: {
            name: string;
            faculty: { name: string };
        };
    };
    search_module?: number;
    supervision: Supervision;
}
