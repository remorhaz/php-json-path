<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

abstract class QueryAstNodeType
{

    public const GET_INPUT = 'get_input';
    public const SET_OUTPUT = 'set_output';
    public const CREATE_FILTER_CONTEXT = 'create_filter_context';
    public const SPLIT = 'split'; // TODO: rename to FETCH_AS_ROOTS?
    public const EVALUATE = 'evaluate';
    public const FILTER = 'filter';
    public const EVALUATE_LOGICAL_OR = 'evaluate_logical_or';
    public const EVALUATE_LOGICAL_AND = 'evaluate_logical_and';
    public const EVALUATE_LOGICAL_NOT = 'evaluate_logical_not';
    public const CALCULATE_IS_EQUAL = 'calculate_is_equal';
    public const CALCULATE_IS_GREATER = 'calculate_is_greater';
    public const CALCULATE_IS_REGEXP = 'calculate_is_regexp';
    public const FETCH_CHILDREN = 'fetch_children';
    public const FETCH_CHILDREN_DEEP = 'fetch_children_deep';
    public const MATCH_ANY_CHILD = 'match_any_child';
    public const MATCH_PROPERTY_STRICTLY = 'match_property_strictly';
    public const MATCH_ELEMENT_STRICTLY = 'match_element_strictly';
    public const AGGREGATE = 'aggregate';
    public const POPULATE_LITERAL = 'populate_literal';
    public const POPULATE_LITERAL_ARRAY = 'populate_literal_array';
    public const POPULATE_INDEX_LIST = 'populate_index_list';
    public const POPULATE_INDEX_SLICE = 'populate_index_slice';
    public const POPULATE_NAME_LIST = 'populate_name_list';

    public const CREATE_SCALAR = 'create_scalar';
    public const CREATE_ARRAY = 'create_array';
    public const APPEND_TO_ARRAY = 'append_to_array';
}